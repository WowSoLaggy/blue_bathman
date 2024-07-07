const csv = require('csv-parser');
const fs = require('fs');
const path = require('path');


let _cache = null;


async function read_users_file() {
  if (!_cache) {
    return new Promise((resolve, reject) => {
      const results = [];
      const csvFilePath = path.resolve(__dirname, './../../tables/users.csv');
      fs.createReadStream(csvFilePath)
        .pipe(csv())
        .on('data', (data) => results.push(data))
        .on('end', () => {
          _cache = results;
          resolve(results);
        })
        .on('error', (err) => reject(err));
    });
  }
  return _cache;
}


async function user_exists(user_id) {
  const users = await read_users_file();
  return users.some(user => user['user_id'] === user_id.toString());
}

async function get_user_subs(user_id) {
  const users = await read_users_file();
  const subs_string = users.find(user => user['user_id'] === user_id.toString())['subs'];
  return subs_string.split(',').map(sub => parseInt(sub));
}


module.exports = { user_exists, get_user_subs };
