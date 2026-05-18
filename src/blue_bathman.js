const { process_callback, process_message } = require('./utils/process.js');
const { notify_users } = require('./utils/notify.js');
const ydb_utils = require('./utils/ydb_utils');


async function cleanup() {
  let ydb = await ydb_utils.get();
  await ydb.destroy();
}


async function on_request(body) {
  try {
    if (body.callback_query) {
      response = await process_callback(body.callback_query);
    }
    else if (body.message) {
      response = await process_message(body.message);
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
  } finally {
    await cleanup();
  }
}


async function on_notify() {
  await notify_users();
  await cleanup();
}


module.exports = { on_request, on_notify };
