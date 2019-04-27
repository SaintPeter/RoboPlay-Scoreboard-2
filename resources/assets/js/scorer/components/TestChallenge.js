import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import {connect} from "react-redux";
import loadSingleChal from "../utils/loadSingleChal";

import RandomsPopup from "./Popups/RandomsPopup";
import RandomListPopup from "./Popups/RandomListPopup";
import UIButton from "./UIButton";
import YesNo from "./ScoreElements/YesNo";
import Slider from "./ScoreElements/Slider";
import Timer from "./ScoreElements/Timer";
import ScoreElement from "./ScoreElements/ScoreElement";
import SubmitConfirmPopup from "./Popups/SubmitConfirmPopup";
import AbortConfirmPopup from "./Popups/AbortConfirmPopup";
import {abortChallenge, scoreChallenge, updateScoreSummary} from "../actions/ScoreChallenge";
import {updateBackButton, updatePageTitle} from "../actions/Generic";

class TestChallengeApp extends Component {
  constructor(props) {
    super(props);
    this.chalId = props.match.params.chalId;

    this.state = {
      total: 0,
      scores: {},
      chalData: {},
      submitConfirmVisible: false,
      abortConfirmVisible: false,
    };

    console.log("Loaded TestChallengeApp");

  }

  componentWillMount() {

  }

  componentDidMount() {
    this.props.updateTitle("Test Challenge");
    this.props.updateBack('', false);
    this.loadChallengeData();
  }

  loadChallengeData = () => {
    loadSingleChal.load(this.props.match.params.chalId, (data) => {
      this.setState({'chalData': data});
    });
  };

  prePopulateStateScores = () => {
    this.setState({
        'scores': this.state.chalData
          .score_elements.reduce((acc, element) => {
            switch (element.type) {
              case 'yesno':
                acc[element.element_number] = element.base_value + element.multiplier;
                break;
              case 'high_slider':
                acc[element.element_number] = element.max_entry * element.multiplier;
                break;
              default:
                acc[element.element_number] = Math.max(element.min_entry, element.base_value);
            }
            // Enforce Min/Max
            if (element.enforce_limits) {
              acc[element.element_number] = Math.min(element.max_entry, Math.max(element.min_entry, acc[element.element_number]));
            }
            return acc;
          }, {})
      },
      this.updateTotalScore);
  };

  updateTotalScore = () => {
    this.setState(
      {
        total: Math.max(0, Object.keys(this.state.scores).reduce((acc, key) => {
          return acc + this.state.scores[key];
        }, 0))
      }
    )
  };

  // Handles the change from an individual score element
  // Updates the state, then updates the total
  scoreChange = (scoreData) => {
    let newState = {scores: Object.assign({}, this.state.scores, scoreData)};
    this.setState(newState,
      this.updateTotalScore);
  };

  challengeType = (type, item) => {
    switch (type) {
      case 'yesno':
        return <YesNo type="yesno" compInfo={this.state} key={item.id} {...item} />;
      case 'noyes':
        return <YesNo type="noyes" compInfo={this.state} key={item.id} {...item} />;
      case 'slider':
      case 'low_slider':
        return <Slider type="low" compInfo={this.state} key={item.id} {...item} />;
      case 'high_slider':
        return <Slider type="high" compInfo={this.state} key={item.id} {...item} />;
      case 'score_slider':
        return <Slider type="score" compInfo={this.state} key={item.id} {...item} />;
      case 'timer':
        return <Timer type="timer" compInfo={this.state} key={item.id} {...item} />;
    }
    return <li>Unknown Type: {type}</li>;
  };

  submitConfirmCanceled = () => {
    this.setState({submitConfirmVisible: false});
  };

  submitConfirmConfirmed = () => {
    this.setState({submitConfirmVisible: false});
  };

  abortConfirmCanceled = () => {
    this.setState({abortConfirmVisible: false});
  };

  abortConfirmConfirmed = () => {
    this.setState({abortConfirmVisible: false});
  };

  render() {
    if (Object.keys(this.state.chalData).length < 1) {
      return (
        <h4>Loading Data</h4>
      )
    } else {
      let elements = (this.state.chalData.score_elements) ? this.state.chalData.score_elements : [];

      return [
        <div className="ui-body ui-body-a ui-corner-all" key='ChallengeHeader'>
          <strong>{this.state.chalData.display_name} ({this.chalId})</strong>
          <hr/>
          <div dangerouslySetInnerHTML={{__html: this.state.chalData.rules}}/>
        </div>,
        (this.state.chalData.randoms && this.state.chalData.randoms.length > 0) ?
          <RandomsPopup randoms={this.state.chalData.randoms} key='RandomsPopup'/> : null
        ,
        (this.state.chalData.random_lists && this.state.chalData.random_lists.length > 0) ?
          <RandomListPopup random_lists={this.state.chalData.random_lists} key='RandomListPopup' /> : null
        ,
        <ul className="ui-listview ui-listview-inset ui-corner-all ui-shadow" key='ScoreElementList'>
          {
            (elements) ? elements.map((item, num) => {
              return (
                <ScoreElement key={item.id} scoreChange={this.scoreChange} {...item}>
                  {this.challengeType(item.type, item)}
                </ScoreElement>
              )
            }) : <li>No Data</li>
          }
          <li className="ui-field-contain ui-li-static ui-body-inherit">
            Estimated Score: {this.state.total} out of {this.state.chalData.points} points
          </li>
          <li className="ui-field-contain ui-li-static ui-body-inherit">
            <fieldset className="ui-grid-b">
              <div className="ui-block-b">
                <UIButton onClick={(e) => {
                  this.setState({submitConfirmVisible: true})
                }}>
                  Submit
                </UIButton>
              </div>
              <div className="ui-block-b">
                <UIButton onClick={this.loadChallengeData}>
                  Reload
                </UIButton>
              </div>
              <div className="ui-block-b">
                <UIButton onClick={(e) => {
                  this.setState({abortConfirmVisible: true})
                }}>
                  Abort
                </UIButton>
              </div>
            </fieldset>
          </li>
        </ul>,
        <SubmitConfirmPopup
          onSubmit={this.submitConfirmConfirmed}
          onCancel={this.submitConfirmCanceled}
          runNumber={this.runNumber}
          visible={this.state.submitConfirmVisible}
          score={this.state.total}
          key='SubmitConfirmPopup'
        />,
        <AbortConfirmPopup
          onAbort={this.abortConfirmConfirmed}
          onCancel={this.abortConfirmCanceled}
          runNumber={this.runNumber}
          visible={this.state.abortConfirmVisible}
          key='AbortConfirmPopup'
        />
      ]
    }
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    teamScores: state.teamScores,
    challengeData: state.challengeData,
    backURL: state.generic.backURL
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    updateBack: (newURL, show = true) => dispatch(updateBackButton(newURL, show)),
    updateTitle: (newTitle) => dispatch(updatePageTitle(newTitle)),
    doLoadChalData: (year, level, data) => dispatch(loadChallengeData(year, level, data)),
    submitScore: (teamId, chalId, divId, scores) => dispatch(scoreChallenge(teamId, chalId, divId, scores)),
    submitAbort: (teamId, chalId, divId, elementCount) => dispatch(abortChallenge(teamId, chalId, divId, elementCount)),
    updateScoreSummary: (teamScores) => dispatch(updateScoreSummary(teamScores))
  }
}

const TestChallenge = connect(
  mapStateToProps,
  mapDispatchToProps
)(TestChallengeApp);

export default TestChallenge;