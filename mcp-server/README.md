# 🔌 MCP MySQL Server untuk PPID Polinema

Server MCP (Model Context Protocol) untuk mengakses database MySQL `polinema_ppid` secara langsung melalui Claude/Copilot.

## 🎯 Fitur

- ✅ **query_database** - Execute SELECT query langsung
- ✅ **get_tables** - List semua tabel
- ✅ **describe_table** - Lihat struktur tabel
- ✅ **get_menu_structure** - Analisis struktur menu per role
- ✅ **analyze_routing** - Trace dynamic routing mechanism

## 📦 Instalasi

```bash
cd mcp-server
npm install
```

## 🚀 Cara Menggunakan

### 1. Via VS Code (Otomatis)

MCP server akan otomatis berjalan ketika Anda menggunakan Copilot/Claude di VS Code.

### 2. Test Manual

```bash
node server.js
```

Lalu test dengan:

```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "method": "tools/call",
  "params": {
    "name": "get_tables",
    "arguments": {}
  }
}
```

## 🔍 Contoh Query

### Get All Tables
```javascript
// Tool: get_tables
// Result: List semua tabel di database
```

### Analyze Menu Routing
```javascript
// Tool: analyze_routing
// Arguments: { "menu_name": "menu-management" }
// Result: Complete routing flow dari database ke URL
```

### Get Menu Structure
```javascript
// Tool: get_menu_structure
// Arguments: { "role": "ADM", "limit": 20 }
// Result: Struktur menu untuk role Administrator
```

### Custom Query
```javascript
// Tool: query_database
// Arguments: { 
//   "query": "SELECT * FROM web_menu_url WHERE isDeleted = 0 LIMIT 10" 
// }
```

## ⚙️ Konfigurasi Database

Edit di `server.js` jika credentials berbeda:

```javascript
const DB_CONFIG = {
  host: '127.0.0.1',
  port: 3306,
  user: 'root',
  password: '',
  database: 'polinema_ppid',
};
```

## 🔒 Keamanan

- ✅ Hanya query SELECT yang diperbolehkan
- ✅ Connection pooling untuk efisiensi
- ✅ Error handling yang proper
- ✅ No write/delete operations

## 📝 Troubleshooting

### Error: Cannot connect to database
```bash
# Pastikan MySQL berjalan
# Cek dengan:
mysql -u root -p polinema_ppid -e "SHOW TABLES;"
```

### Error: Module not found
```bash
# Install ulang dependencies
npm install
```

### Error: Permission denied
```bash
# Jalankan VS Code as Administrator
```

## 🎓 Dokumentasi MCP

- [MCP Specification](https://spec.modelcontextprotocol.io/)
- [SDK Documentation](https://github.com/modelcontextprotocol/sdk)

## 👨‍💻 Author

Created for PPID Polinema Project
