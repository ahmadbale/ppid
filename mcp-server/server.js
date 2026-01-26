#!/usr/bin/env node

/**
 * MCP Server for PPID Polinema MySQL Database
 * Provides tools to query and analyze database
 */

import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
} from '@modelcontextprotocol/sdk/types.js';
import mysql from 'mysql2/promise';

// Database configuration from .env
const DB_CONFIG = {
  host: '127.0.0.1',
  port: 3306,
  user: 'root',
  password: '',
  database: 'polinema_ppid',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
};

class PPIDMySQLServer {
  constructor() {
    this.server = new Server(
      {
        name: 'ppid-mysql-server',
        version: '1.0.0',
      },
      {
        capabilities: {
          tools: {},
        },
      }
    );

    this.pool = null;
    this.setupHandlers();
    this.setupErrorHandling();
  }

  async setupHandlers() {
    // List available tools
    this.server.setRequestHandler(ListToolsRequestSchema, async () => ({
      tools: [
        {
          name: 'query_database',
          description: 'Execute SELECT query on PPID database. Returns results as JSON.',
          inputSchema: {
            type: 'object',
            properties: {
              query: {
                type: 'string',
                description: 'SQL SELECT query to execute (only SELECT allowed for safety)',
              },
            },
            required: ['query'],
          },
        },
        {
          name: 'get_tables',
          description: 'List all tables in the PPID database',
          inputSchema: {
            type: 'object',
            properties: {},
          },
        },
        {
          name: 'describe_table',
          description: 'Get structure/schema of a specific table',
          inputSchema: {
            type: 'object',
            properties: {
              table: {
                type: 'string',
                description: 'Table name to describe',
              },
            },
            required: ['table'],
          },
        },
        {
          name: 'get_menu_structure',
          description: 'Get complete menu structure with relationships (web_menu, web_menu_global, web_menu_url)',
          inputSchema: {
            type: 'object',
            properties: {
              role: {
                type: 'string',
                description: 'Role code (e.g., ADM, SAR, VFR) - optional',
              },
              limit: {
                type: 'number',
                description: 'Limit results (default: 50)',
              },
            },
          },
        },
        {
          name: 'analyze_routing',
          description: 'Analyze dynamic routing configuration - shows how URLs are resolved',
          inputSchema: {
            type: 'object',
            properties: {
              menu_name: {
                type: 'string',
                description: 'Menu URL name to analyze (e.g., menu-management)',
              },
            },
          },
        },
      ],
    }));

    // Handle tool calls
    this.server.setRequestHandler(CallToolRequestSchema, async (request) => {
      try {
        if (!this.pool) {
          this.pool = mysql.createPool(DB_CONFIG);
        }

        switch (request.params.name) {
          case 'query_database':
            return await this.queryDatabase(request.params.arguments);
          
          case 'get_tables':
            return await this.getTables();
          
          case 'describe_table':
            return await this.describeTable(request.params.arguments);
          
          case 'get_menu_structure':
            return await this.getMenuStructure(request.params.arguments);
          
          case 'analyze_routing':
            return await this.analyzeRouting(request.params.arguments);
          
          default:
            throw new Error(`Unknown tool: ${request.params.name}`);
        }
      } catch (error) {
        return {
          content: [
            {
              type: 'text',
              text: `Error: ${error.message}\nStack: ${error.stack}`,
            },
          ],
          isError: true,
        };
      }
    });
  }

  async queryDatabase(args) {
    const { query } = args;

    // Security: Only allow SELECT queries
    if (!query.trim().toUpperCase().startsWith('SELECT')) {
      throw new Error('Only SELECT queries are allowed for safety');
    }

    const [rows] = await this.pool.execute(query);
    
    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify(rows, null, 2),
        },
      ],
    };
  }

  async getTables() {
    const [tables] = await this.pool.execute('SHOW TABLES');
    const tableNames = tables.map(row => Object.values(row)[0]);
    
    return {
      content: [
        {
          type: 'text',
          text: `📊 Tables in polinema_ppid:\n\n${tableNames.join('\n')}`,
        },
      ],
    };
  }

  async describeTable(args) {
    const { table } = args;
    const [columns] = await this.pool.execute(`DESCRIBE ${table}`);
    
    return {
      content: [
        {
          type: 'text',
          text: `📋 Structure of table '${table}':\n\n${JSON.stringify(columns, null, 2)}`,
        },
      ],
    };
  }

  async getMenuStructure(args) {
    const { role = null, limit = 50 } = args;
    
    let query = `
      SELECT 
        wm.web_menu_id,
        wm.fk_web_menu_global,
        wm.fk_m_hak_akses,
        ha.hak_akses_kode,
        ha.hak_akses_nama,
        wm.wm_parent_id,
        wm.wm_menu_nama,
        wm.wm_status_menu,
        wm.wm_urutan_menu,
        wmg.wmg_nama_default,
        wmu.wmu_nama as url,
        wmu.web_menu_url_id
      FROM web_menu wm
      INNER JOIN m_hak_akses ha ON wm.fk_m_hak_akses = ha.hak_akses_id
      LEFT JOIN web_menu_global wmg ON wm.fk_web_menu_global = wmg.web_menu_global_id
      LEFT JOIN web_menu_url wmu ON wmg.fk_web_menu_url = wmu.web_menu_url_id
      WHERE wm.isDeleted = 0
    `;
    
    if (role) {
      query += ` AND ha.hak_akses_kode = ?`;
    }
    
    query += ` ORDER BY ha.hak_akses_kode, wm.wm_urutan_menu LIMIT ${limit}`;
    
    const [rows] = role 
      ? await this.pool.execute(query, [role])
      : await this.pool.execute(query);
    
    return {
      content: [
        {
          type: 'text',
          text: `🗂️ Menu Structure (${rows.length} items):\n\n${JSON.stringify(rows, null, 2)}`,
        },
      ],
    };
  }

  async analyzeRouting(args) {
    const { menu_name } = args;
    
    // Step 1: Find URL in web_menu_url
    const [urlData] = await this.pool.execute(`
      SELECT 
        wmu.*,
        app.app_key,
        app.app_name
      FROM web_menu_url wmu
      LEFT JOIN m_application app ON wmu.fk_m_application = app.application_id
      WHERE wmu.wmu_nama = ? AND wmu.isDeleted = 0
    `, [menu_name]);
    
    // Step 2: Find related web_menu_global
    const [globalData] = await this.pool.execute(`
      SELECT * FROM web_menu_global 
      WHERE fk_web_menu_url = ? AND isDeleted = 0
    `, [urlData[0]?.web_menu_url_id]);
    
    // Step 3: Find all web_menu using this global
    const [menuData] = await this.pool.execute(`
      SELECT 
        wm.*,
        ha.hak_akses_kode,
        ha.hak_akses_nama
      FROM web_menu wm
      INNER JOIN m_hak_akses ha ON wm.fk_m_hak_akses = ha.hak_akses_id
      WHERE wm.fk_web_menu_global IN (${globalData.map(() => '?').join(',')})
        AND wm.isDeleted = 0
    `, globalData.map(g => g.web_menu_global_id));
    
    const analysis = {
      menu_name,
      url_data: urlData[0] || null,
      global_menus: globalData,
      role_menus: menuData,
      routing_flow: [
        `1. getDynamicMenuUrl('${menu_name}') called`,
        `2. Query web_menu_url WHERE wmu_nama = '${menu_name}'`,
        `3. Found URL ID: ${urlData[0]?.web_menu_url_id}`,
        `4. Used by ${globalData.length} global menu(s)`,
        `5. Configured for ${menuData.length} role(s): ${[...new Set(menuData.map(m => m.hak_akses_kode))].join(', ')}`,
        `6. Final route prefix: /${menu_name}`,
      ]
    };
    
    return {
      content: [
        {
          type: 'text',
          text: `🔍 Routing Analysis:\n\n${JSON.stringify(analysis, null, 2)}`,
        },
      ],
    };
  }

  setupErrorHandling() {
    this.server.onerror = (error) => {
      console.error('[MCP Error]', error);
    };

    process.on('SIGINT', async () => {
      if (this.pool) {
        await this.pool.end();
      }
      await this.server.close();
      process.exit(0);
    });
  }

  async run() {
    const transport = new StdioServerTransport();
    await this.server.connect(transport);
    console.error('PPID MySQL MCP server running on stdio');
  }
}

// Start server
const server = new PPIDMySQLServer();
server.run().catch(console.error);
