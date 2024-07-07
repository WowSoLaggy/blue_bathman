const { send_message } = require('tg_api');

const { get_bdays_2_formatted, get_bdays_12_formatted } = require('./bdays');


const commands = [
  { name: '/bd', handler: async (token, user_id, chat_id, text) => {
    return await send_message(token, chat_id, await get_bdays_2_formatted(user_id));
  }},

  { name: '/all', handler: async (token, user_id, chat_id, text) => {
    return await send_message(token, chat_id, await get_bdays_12_formatted(user_id));
  }},

  { name: '/start', handler: async (token, user_id, chat_id, text) => {
    return await send_message(token, chat_id,
      `Привет! Я бот - Голубой Банщик.

Я умею показывать дни рождения, если ты меня попросишь:
/bd (или любое другое слово) - ближайшие 2 месяца
/all - на весь год

У меня внизу есть меню, где можно посмотреть все команды.`);
  }},

  { name: '/help', handler: async (token, user_id, chat_id, text) => {
    // The same as /start
    return await commands.find(c => c.name === '/start').handler(token, user_id, chat_id, text);
  }},
];


module.exports = commands;
