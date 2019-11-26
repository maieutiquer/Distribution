import React from 'react'
import {PropTypes as T} from 'prop-types'

import {withRouter}from '#/main/app/router'
import {trans} from '#/main/app/intl/translation'
import {SecurityPage} from '#/main/app/security/containers/page'

import {RegistrationMain} from '#/main/app/security/registration/containers/main'

const HomeRegistrationComponent = (props) =>
  <SecurityPage>
    <RegistrationMain
      path="/registration"
      onRegister={() => {
        props.history.push('/desktop')
      }}
    />
  </SecurityPage>

HomeRegistrationComponent.propTypes = {
  history: T.shape({
    push: T.func.isRequired
  }).isRequired
}

const HomeRegistration = withRouter(HomeRegistrationComponent)

export {
  HomeRegistration
}
