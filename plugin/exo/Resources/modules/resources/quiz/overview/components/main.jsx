import React from 'react'
import {PropTypes as T} from 'prop-types'

import {trans} from '#/main/app/intl/translation'
import {displayDuration} from '#/main/app/intl/date'
import {LINK_BUTTON} from '#/main/app/buttons'
import {UserEvaluation as UserEvaluationType} from '#/main/core/resource/prop-types'
import {ResourceOverview} from '#/main/core/resource/components/overview'

import {correctionModes, markModes, SHOW_CORRECTION_AT_DATE, SHOW_SCORE_AT_NEVER} from '#/plugin/exo/quiz/enums'

// TODO : show info about number of attempts

const Parameters = props =>
  <ul className="exercise-parameters">
    <li className="exercise-parameter">
      <span className="fa fa-fw fa-check-square-o icon-with-text-right" />
      {trans('results_availability', {}, 'quiz')} :
      &nbsp;
      <b>
        {props.showCorrectionAt === SHOW_CORRECTION_AT_DATE ?
          props.correctionDate :
          trans(correctionModes.find(mode => mode[0] === props.showCorrectionAt)[1], {}, 'quiz')
        }
      </b>
    </li>

    <li className="exercise-parameter">
      <span className="fa fa-fw fa-percent icon-with-text-right" />
      {trans('score_availability', {}, 'quiz')} :
      &nbsp;
      <b>{trans(markModes.find(mode => mode[0] === props.showScoreAt)[1], {}, 'quiz')}</b>
    </li>

    <li className="exercise-parameter">
      <span className="fa fa-fw fa-sign-out icon-with-text-right" />
      {trans('test_exit', {}, 'quiz')} :
      &nbsp;
      <b>{props.interruptible ? trans('yes') : trans('no')}</b>
    </li>

    <li className="exercise-parameter">
      <span className="fa fa-fw fa-files-o icon-with-text-right" />
      {trans('maximum_attempts', {}, 'quiz')} :
      &nbsp;
      <b>{props.maxAttempts ? props.maxAttempts : '-'}</b>
    </li>

    {(props.timeLimited && props.duration) &&
      <li className="exercise-parameter">
        <span className="fa fa-fw fa-clock-o icon-with-text-right" />
        {trans('duration')} :
        &nbsp;
        <b>{displayDuration(props.duration)}</b>
      </li>
    }
  </ul>

Parameters.propTypes = {
  showCorrectionAt: T.string.isRequired,
  correctionDate: T.string,
  showScoreAt: T.string.isRequired,
  interruptible: T.bool.isRequired,
  maxAttempts: T.number,
  timeLimited: T.bool.isRequired,
  duration: T.number
}

Parameters.defaultProps = {
  timeLimited: false
}

const OverviewMain = props =>
  <ResourceOverview
    contentText={props.quiz.description}
    progression={{
      status: props.userEvaluation ? props.userEvaluation.status : undefined,
      statusTexts: {
        opened: trans('exercise_status_opened_message', {}, 'quiz'),
        completed: trans('exercise_status_completed_message', {}, 'quiz'),
        passed: trans('exercise_status_passed_message', {}, 'quiz'),
        failed: trans('exercise_status_failed_message', {}, 'quiz')
      },
      score: {
        displayed: props.quiz.parameters.showScoreAt !== SHOW_SCORE_AT_NEVER,
        current: props.userEvaluation ? props.userEvaluation.score : undefined,
        total: props.userEvaluation ? props.userEvaluation.scoreMax : undefined
      },
      feedback: {
        displayed: false, // FIXME
        success: props.quiz.parameters.successMessage,
        failure: props.quiz.parameters.failureMessage
      }
    }}
    actions={[
      {
        type: LINK_BUTTON,
        icon: 'fa fa-fw fa-play',
        label: trans('exercise_start', {}, 'quiz'),
        target: `${props.path}/play`,
        primary: true,
        disabled: props.empty,
        disabledMessages: [
          trans('start_disabled_empty', {}, 'quiz')
        ]
      }
    ]}
  >
    <section className="resource-parameters">
      <h3 className="h2">{trans('configuration')}</h3>

      <Parameters
        showCorrectionAt={props.quiz.parameters.showCorrectionAt}
        correctionDate={props.quiz.parameters.correctionDate}
        showScoreAt={props.quiz.parameters.showScoreAt}
        interruptible={props.quiz.parameters.interruptible}
        maxAttempts={props.quiz.parameters.maxAttempts}
        timeLimited={props.quiz.parameters.timeLimited}
        duration={props.quiz.parameters.duration}
      />
    </section>
  </ResourceOverview>

OverviewMain.propTypes = {
  path: T.string.isRequired,
  empty: T.bool.isRequired,
  editable: T.bool.isRequired,
  quiz: T.shape({
    description: T.string,
    parameters: T.object.isRequired,
    picking: T.object.isRequired
  }).isRequired,
  userEvaluation: T.shape(
    UserEvaluationType.propTypes
  )
}

export {
  OverviewMain
}
