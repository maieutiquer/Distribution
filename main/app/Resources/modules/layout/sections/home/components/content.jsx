import React from 'react'
import {PropTypes as T} from 'prop-types'

import {PageSimple} from '#/main/app/page/components/simple'
import {ToolMain} from '#/main/core/tool/containers/main'

const HomeContent = props => {
  switch (props.type) {
    case 'html':
      return (
        <PageSimple>
          {props.content}
        </PageSimple>
      )

    case 'tool':
      return (
        <ToolMain />
      )
  }

  return null
}

HomeContent.propTypes = {
  type: T.string.isRequired,
  content: T.string
}

export {
  HomeContent
}
