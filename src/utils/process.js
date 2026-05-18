const bot = require('./bot');

const commands = require('./commands');
const { user_exists } = require('./users');


async function process(user_id, chat_id, text) {
  console.log('Processing:', user_id, chat_id, text);

  if (!await user_exists(user_id)) {
    return await bot.sendMessage(chat_id, 'You are not authorized');
  }

  const command_name = text.split(' ')[0];
  const command = commands.find(c => c.name === command_name) || commands[0];
  return await command.handler(user_id, chat_id, text);
}


async function process_callback(callback) {
  return await process(callback.from.id, callback.message.chat.id, callback.data);
}

async function process_message(message) {
  return await process(message.from.id, message.chat.id, message.text);
}


module.exports = { process_callback, process_message };
