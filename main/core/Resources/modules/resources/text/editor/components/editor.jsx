import React, {Component} from 'react'
import {connect} from 'react-redux'
import {PropTypes as T} from 'prop-types'
import cloneDeep from 'lodash/cloneDeep'

import {trans} from '#/main/app/intl/translation'
import {LINK_BUTTON} from '#/main/app/buttons'
import {FormData} from '#/main/app/content/form/containers/data'

import {selectors as resourceSelectors} from '#/main/core/resource/store'
import {selectors} from '#/main/core/resources/text/editor/store'
import {Text as TextTypes} from '#/main/core/resources/text/prop-types'

import { actions as formActions } from '#/main/app/content/form/store'
import { actions as mercureActions } from '#/main/core/mercure/actions'

import {param} from '#/main/app/config/parameters'

class EditorComponent extends Component {

  componentDidMount() {
    if (param('mercure.enabled')) {
      this.props.getTemporary(this.props.text)
      const u = new URL(param('mercure.hub_url'))
      u.searchParams.append('topic', 'http://localhost/' + this.props.text.id)
      const es = new EventSource(u)

      es.onmessage = e => {
        this.props.loadText(JSON.parse(e.data))
      }

    }
  }

  render() {
    return <FormData
      name={selectors.FORM_NAME}
      target={['apiv2_resource_text_update', {id: this.props.text.id}]}
      buttons={true}
      cancel={{
        type: LINK_BUTTON,
        target: '/',
        exact: true
      }}
      sections={[
        {
          title: trans('general', {}, 'platform'),
          primary: true,
          fields: [
            {
              name: 'content',
              type: 'html',
              label: trans('text'),
              hideLabel: true,
              required: true,
              onChange: (content) => {
                //c'est un peu brutal non ?
                const newText = cloneDeep(this.props.text)
                newText.content = content
                this.props.publish(newText)
              },
              options: {
                workspace: this.props.workspace,
                minRows: 3
              }
            }
          ]
        }
      ]}
    />

  }
}

EditorComponent.propTypes = {
  workspace: T.object,
  text: T.shape(
    TextTypes.propTypes
  ).isRequired,
  loadText: T.func.isRequired,
  publish: T.func.isRequired,
  getTemporary: T.func.isRequired
}

const Editor = connect(
  state => ({
    workspace: resourceSelectors.workspace(state),
    text: selectors.text(state)
  }),
  (dispatch) => ({
    loadText(text) {
      dispatch(formActions.resetForm(selectors.FORM_NAME, text))
    },
    getTemporary(text) {
      dispatch(mercureActions.get(selectors.FORM_NAME, text))
    },
    publish(text) {
      dispatch(mercureActions.publish(text))
    }
  })
)(EditorComponent)

export {
  Editor
}
