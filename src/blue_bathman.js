const { process_callback, process_message } = require('./utils/process.js');
const { notify_users } = require('./utils/notify.js');


async function on_request(token, body) {
  try {
    if (body.callback_query) {
      response = await process_callback(token, body.callback_query);
    }
    else if (body.message) {
      response = await process_message(token, body.message);
    }
    else {
      throw new Error('No message or callback_query');
    }
    
    return {
        statusCode: 200,
        body: JSON.stringify(response),
    };
  }
  catch (error) {
    return {
        status: 500,
        body: error,
    };
  }
}


async function on_notify(token) {
  await notify_users(token);
}


module.exports = { on_request, on_notify };
