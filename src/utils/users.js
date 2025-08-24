const ydb_utils = require('./ydb_utils');


let _cache = null;


async function get_users() {
  if (!_cache) {
    query = `SELECT * FROM \`bdays/users_tbl\``;

    try {
      const ydb = await ydb_utils.get();
      _cache = await ydb.query(query);
    } catch (error) {
      console.error('Error fetching users:', error);
      throw error;
    }
  }

  return _cache;
}


async function user_exists(user_id) {
  const users = await get_users();
  return users.some(user => user['user_id'] == user_id);
}

async function get_user_subs(user_id) {
  const users = await get_users();
  const user = users.find(user => user['user_id'] == user_id);
  const subs_string = user['subs'];
  return subs_string.split(',').map(sub => parseInt(sub));
}


async function get_users_to_notify() {
  const users = await get_users();
  return users.filter(user => user['notify'] === 1);
}


module.exports = { user_exists, get_user_subs, get_users_to_notify };
