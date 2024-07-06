const { send_message } = require('tg_api');


async function process(token, user_id, chat_id, text) {
  const response = await send_message(token, chat_id, text);
  return response;
}


async function process_callback(token, callback) {
  return await process(token, callback.from.id, callback.message.chat.id, callback.data);
}

async function process_message(token, message) {
  return await process(token, message.from.id, message.chat.id, message.text);
}

module.exports = { process_callback, process_message };
