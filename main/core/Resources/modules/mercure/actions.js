import {API_REQUEST} from '#/main/app/api'

export const actions = {}

actions.publish = (data) => ({
  [API_REQUEST]: {
    url: ['claro_mercure_publish', { uuid: data.id }],
    request: {
      method: 'POST',
      body: JSON.stringify(data)
    }
  }
})
