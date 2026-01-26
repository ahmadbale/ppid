import mysql from 'mysql2/promise';

async function testConnection() {
  console.log('🔌 Testing MySQL connection...\n');
  
  try {
    const connection = await mysql.createConnection({
      host: '127.0.0.1',
      port: 3306,
      user: 'root',
      password: '',
      database: 'polinema_ppid'
    });
    
    console.log('✅ Connected to database!\n');
    
    // Test query
    const [tables] = await connection.execute('SHOW TABLES');
    console.log(`📊 Found ${tables.length} tables:\n`);
    tables.slice(0, 10).forEach(row => {
      console.log(`   - ${Object.values(row)[0]}`);
    });
    
    // Test menu URL query
    console.log('\n🔍 Testing menu URL query...\n');
    const [urls] = await connection.execute(`
      SELECT wmu_nama, fk_m_application 
      FROM web_menu_url 
      WHERE isDeleted = 0 
      LIMIT 5
    `);
    console.log('Sample URLs:', urls);
    
    await connection.end();
    console.log('\n✅ All tests passed!');
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    process.exit(1);
  }
}

testConnection();
