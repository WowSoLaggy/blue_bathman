const { send_message } = require('tg_api');


async function on_request(token, event) {
  try {
    const response = await send_message(token, event.chat.id, event.text);
    return {
        status: 200,
        body: response
    };
  }
  catch (error) {
    return {
        status: 500,
        body: error
    };
  }
}

module.exports = { on_request };
