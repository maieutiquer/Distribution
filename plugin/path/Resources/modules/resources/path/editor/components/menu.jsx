import React from 'react'
import {PropTypes as T} from 'prop-types'
import classes from 'classnames'
import get from 'lodash/get'

import {matchPath} from '#/main/app/router'
import {trans} from '#/main/app/intl/translation'
import {CALLBACK_BUTTON, LINK_BUTTON, MODAL_BUTTON} from '#/main/app/buttons'
import {Summary} from '#/main/app/content/components/summary'

import {MODAL_STEP_POSITION} from '#/plugin/path/resources/path/editor/modals/position'

const EditorMenu = props => {
  function getStepSummary(step) {
    return {
      type: LINK_BUTTON,
      icon: classes('step-progression fa fa-fw fa-circle', get(step, 'userProgression.status')),
      label: step.title,
      target: `${props.path}/edit/${step.slug}`,
      active: !!matchPath(props.location.pathname, {path: `${props.path}/edit/${step.slug}`}),
      additional: [
        {
          name: 'add',
          type: CALLBACK_BUTTON,
          icon: 'fa fa-fw fa-plus',
          label: trans('step_add_child', {}, 'path'),
          callback: () => {
            const newSlug = props.addStep(props.steps, step)
            props.history.push(`${props.path}/edit/${newSlug}`)
          },
          group: trans('management')
        }, {
          name: 'copy',
          type: MODAL_BUTTON,
          icon: 'fa fa-fw fa-clone',
          label: trans('copy', {}, 'actions'),
          modal: [MODAL_STEP_POSITION, {
            icon: 'fa fa-fw fa-clone',
            title: trans('copy'),
            step: step,
            steps: props.steps,
            selectAction: (position) => ({
              type: CALLBACK_BUTTON,
              label: trans('copy', {}, 'actions'),
              callback: () => props.copyStep(step.id, position)
            })
          }],
          group: trans('management')
        }, {
          name: 'move',
          type: MODAL_BUTTON,
          icon: 'fa fa-fw fa-arrows',
          label: trans('move', {}, 'actions'),
          modal: [MODAL_STEP_POSITION, {
            icon: 'fa fa-fw fa-arrows',
            title: trans('movement'),
            step: step,
            steps: props.steps,
            selectAction: (position) => ({
              type: CALLBACK_BUTTON,
              label: trans('move', {}, 'actions'),
              callback: () => props.moveStep(step.id, position)
            })
          }],
          group: trans('management')
        }, {
          name: 'delete',
          type: CALLBACK_BUTTON,
          icon: 'fa fa-fw fa-trash-o',
          label: trans('delete', {}, 'actions'),
          callback: () => {
            props.removeStep(step.id)
            if (`${props.path}/edit/${step.slug}` === props.location.pathname) {
              props.history.push(`${props.path}/edit`)
            }
          },
          confirm: {
            title: trans('deletion'),
            subtitle: step.title,
            message: trans('step_delete_confirm', {}, 'path')
          },
          dangerous: true,
          group: trans('management')
        }
      ],
      children: step.children ? step.children.map(getStepSummary) : []
    }
  }

  return (
    <Summary
      links={[{
        type: LINK_BUTTON,
        icon: 'fa fa-fw fa-cog',
        label: trans('parameters'),
        target: `${props.path}/edit/parameters`
      }].concat(props.steps.map(getStepSummary), [{
        type: CALLBACK_BUTTON,
        icon: 'fa fa-fw fa-plus',
        label: trans('step_add', {}, 'path'),
        callback: () => {
          const newSlug = props.addStep(props.steps)
          props.history.push(`${props.path}/edit/${newSlug}`)
        }
      }])}
    />
  )
}

EditorMenu.propTypes = {
  history: T.shape({
    push: T.func.isRequired
  }).isRequired,
  location: T.shape({
    pathname: T.string.isRequired
  }).isRequired,
  path: T.string.isRequired,
  steps: T.arrayOf(T.shape({
    // TODO : step types
  })),
  addStep: T.func.isRequired,
  copyStep: T.func.isRequired,
  moveStep: T.func.isRequired,
  removeStep: T.func.isRequired
}

EditorMenu.defaultProps = {
  steps: []
}

export {
  EditorMenu
}