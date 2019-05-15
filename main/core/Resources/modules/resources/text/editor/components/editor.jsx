import React, {Component} from 'react'
import {connect} from 'react-redux'
import {PropTypes as T} from 'prop-types'

import {trans} from '#/main/app/intl/translation'
import {LINK_BUTTON} from '#/main/app/buttons'
import {FormData} from '#/main/app/content/form/containers/data'

import {selectors as resourceSelectors} from '#/main/core/resource/store'
import {selectors} from '#/main/core/resources/text/editor/store'
import {Text as TextTypes} from '#/main/core/resources/text/prop-types'

class EditorComponent extends Component {

  componentDidMount() {
    const u = new URL('http://localhost:3000/hub')
    u.searchParams.append('topic', 'http://localhost/text')
    const es = new EventSource(u)
    es.onmessage = e => {
      console.log(e.data)
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
      lock={{
        id: this.props.text.id,
        className: 'Claroline\\CoreBundle\\Entity\\Resource\\Text',
        autoUnlock: true
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
  ).isRequired
}

const Editor = connect(
  state => ({
    workspace: resourceSelectors.workspace(state),
    text: selectors.text(state)
  })
)(EditorComponent)

export {
  Editor
}
