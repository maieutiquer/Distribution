import {API_REQUEST} from '#/main/app/api'
import { actions as formActions } from '#/main/app/content/form/store'

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

actions.get = (formName, data) => ({
  [API_REQUEST]: {
    url: ['claro_mercure_get', { uuid: data.id }],
    request: { method: 'GET' },
    success: (response, dispatch) => {
      dispatch(formActions.resetForm(formName, data))
    }
  }
})
