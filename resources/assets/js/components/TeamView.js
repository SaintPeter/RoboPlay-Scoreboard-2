import React, { Component } from "react";
import ChallengeSection from "./ChallengeSection";
import {updateBackButton, updatePageTitle} from "../actions/Generic";
import {loadScores} from "../actions/ScoreChallenge";
import {connect} from "react-redux";
import UIButton from "./UIButton";

/*  Class to display scoreboard for a single team
*   props: teamName - string
*          teanInfo - array of team scores for various challenges
*          challenges - array of challenge objects
*/

class TeamViewApp extends Component {
  constructor(props) {
    super(props);
    this.compId = props.match.params.compId;
    this.divId = props.match.params.divId;
    this.teamId = props.match.params.teamId;
    this.year = compData[this.compId].year;

    this.level = compData[this.compId].divisions[this.divId].level;
    this.teamName = compData[this.compId].divisions[this.divId].teams[this.teamId];
    this.scores = (this.props.teamScores[this.teamId]) ? this.props.teamScores[this.teamId] : [];
    this.challengeData = this.props.challengeData[this.year][this.level];

    this.backURL = `/c/${this.compId}/d/${this.divId}/t/${this.teamId}`;

    this.state = {
      accordians: {},
      expanded: false
    };
  }

  componentWillMount() {
      this.props.updateBack(this.backURL);
      this.props.updateTitle('Team Score View');
  }

  componentDidMount() {
      this.$node = $(this.refs.collapsibleset);
      this.$node.collapsibleset({
          'inset': true
      });
      // Disable auto-collapse
      $('.ui-collapsible-set').unbind('collapsibleexpand');
  }

  componentWillReceiveProps(nextProps) {
      if(this.props.teamScores[this.teamId] !== nextProps.teamScores[this.teamId]) {
          this.scores = (nextProps.teamScores[this.teamId]) ? nextProps.teamScores[this.teamId] : [];
      }
  }

  expandAll() {
    const accordians = this.state.accordians;
    const showOrHide = this.state.expanded ? "collapse" : "expand";

    for (const key in accordians) {
      if (accordians.hasOwnProperty(key)) {
        accordians[key].collapsible(showOrHide);
      }
    }
    this.setState({ expanded: !this.state.expanded });
  }

  loadScores = () => {
      this.props.loadScores(this.teamId);
  };

  goToTeamScoresView = () => {
      window.open(`/team/${this.teamId}`,'_blank');
  }

  render() {
    const scores = this.scores;
    const challengeData = this.challengeData;
    const accordianObject = this.state.accordians;

    return (
      <div>
          <div className="ui-body ui-body-a ui-corner-all">
        <h4>{this.teamName} Scores</h4>
          </div>
        <div style={{ textAlign: "right" }}>
          <UIButton onClick={() => this.expandAll()} >
            {this.state.expanded ? "Collapse " : "Expand "}All
          </UIButton>
        </div>
        <div ref="collapsibleset" data-role="collapsibleset" data-inset="true">
            {challengeData.map(challenge => {
              return (
                <ChallengeSection
                  challengeData={challenge}
                  key={challenge.id}
                  challengeId={challenge.id}
                  scores={scores}
                  accordianObject={accordianObject}
                />
              );
            })}
        </div>
          <UIButton onClick={this.loadScores}>
              Force Reload Scores
          </UIButton>
          <UIButton onClick={this.goToTeamScoresView}>
              Edit Scores
          </UIButton>
      </div>
    );
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
    return {
        challengeData: state.challengeData,
        teamScores: state.teamScores
    }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
    return {
        updateBack: (newURL, show) => dispatch(updateBackButton(newURL, show)),
        updateTitle: (newTitle) => dispatch(updatePageTitle(newTitle)),
        loadScores: (teamId) => dispatch(loadScores(teamId))
    }
}

const TeamView =  connect(
    mapStateToProps,
    mapDispatchToProps
)(TeamViewApp);

export default TeamView;
