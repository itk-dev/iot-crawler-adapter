const convict = require('convict')
const fs = require('fs')

// Define a schema
const config = convict({
  env: {
    doc: 'The application environment.',
    format: ['production', 'development', 'test'],
    default: 'development',
    env: 'NODE_ENV'
  },
  verbose: {
    doc: 'Output information on data received',
    format: 'Boolean',
    default: false
  },
  devices: {
    wss_url: {
      doc: 'Web-socket url',
      format: 'String',
      default: 'wss://ws.smartcitizen.me'
    },
    ids: {
      doc: 'List of device ids or uuids',
      default: []
    }
  },
  adapter: {
    api_token: {
      doc: 'Api token',
      format: 'String',
      default: null
    },
    post: {
      url: {
        doc: 'Where to post data',
        format: 'String',
        default: 'https://127.0.0.1:8000/smartcitizen'
      },
      options: {
        doc: 'Post options',
        format: 'Object',
        default: {}
      }
    }
  }
})

// Load environment dependent configuration
const env = config.get('env')
const configFilenames = [
  `./config/${env}.json`,
  './config/local.json'
]
for (const filename of configFilenames) {
  if (fs.existsSync(filename)) {
    config.loadFile(filename)
  }
}

// Perform validation
config.validate({ allowed: 'strict' })

module.exports = config
