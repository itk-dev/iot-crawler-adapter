const https = require('https')
const io = require('socket.io-client')
const config = require('./config')

const verbose = config.get('verbose')

const info = function () {
  if (verbose) {
    console.log.apply(console.log, arguments)
  }
}

const deviceIds = config.get('devices.ids')

const postPayload = (payload) => {
  const data = JSON.stringify(payload)
  const url = config.get('adapter.post.url')
  const options = {
    ...{
      method: 'POST',
      headers: {
        'content-type': 'application/json; charset=utf-8',
        'content-length': Buffer.byteLength(data, 'utf8'),
        'authorization': 'token '+config.get('adapter.api_token')
      }
    },
    ...config.get('adapter.post.options')
  }

  const req = https.request(url, options, res => {
    info(`statusCode: ${res.statusCode}`)
  })

  req.write(data)
  req.end()
}

io.connect(config.get('devices.wss_url'))
  .on('data-received', (device) => {
    info(device.data.recorded_at, device.uuid, device.id)
    if (deviceIds.includes(device.id) || deviceIds.includes(device.uuid)) {
      info(JSON.stringify(device))
      postPayload(device)
    }
  })
