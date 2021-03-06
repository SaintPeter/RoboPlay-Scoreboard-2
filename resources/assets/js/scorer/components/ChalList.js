import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import {connect} from "react-redux";
import {loadChallengeData} from "../actions/ChallengeData";
import {loadNominations, saveNominations} from "../actions/Nominations";
import {updateBackButton, updatePageTitle} from "../actions/Generic";
import {loadRuns} from "../actions/Runs";
import UIButton from "./UIButton";
import NominatePopup from "./Popups/NominatePopup";

class ChalListApp extends Component {
  constructor(props) {
    super(props);
    this.compId = props.match.params.compId;
    this.divId = props.match.params.divId;
    this.teamId = props.match.params.teamId;
    this.year = compData[this.compId].year;
    this.level = compData[this.compId].divisions[this.divId].level;
    this.competitionName = compData[this.compId].name;
    this.divisionName = compData[this.compId].divisions[this.divId].name;
    this.teamName = compData[this.compId].divisions[this.divId].teams[this.teamId];

    this.state = {
      nominateVisible: false
    }
  }

  componentWillMount() {
    this.props.updateTitle("Select Challenge");
    this.props.updateBack(`/c/${this.compId}/d/${this.divId}`);

    this.props.doLoadChalData(this.year, this.level);
    this.props.loadNominations(this.teamId);
  }

  componentDidMount() {
    this.props.loadRuns(this.teamId);
  }

  showTeamScoreClick = () => {
    this.props.history.push(`/c/${this.compId}/d/${this.divId}/t/${this.teamId}/scores`);
  };

  showNominatePopupClick = () => {
    this.setState({nominateVisible: true});
  };

  hideNominatePopupClick = () => {
    this.setState({nominateVisible: false});
  };

  saveNoms = (values) => {
    this.props.saveNominations(this.teamId, values);
    this.hideNominatePopupClick();
  };


  render() {
    let challenges = (this.props.challengeData[this.year] && this.props.challengeData[this.year][this.level]) ?
      this.props.challengeData[this.year][this.level] : [];

    let noms = this.props.nominations.hasOwnProperty(this.teamId) ? this.props.nominations[this.teamId]
      : {spirit: 0, teamwork: 0, persevere: 0};

    return [
      <div className="ui-body ui-body-a ui-corner-all" key='ChalListHeader'>
        <strong>Competition:</strong> {this.competitionName}<br/>
        <strong>Division: </strong>{this.divisionName}<br/>
        <strong>Team: </strong>{this.teamName}<br/>
        <strong>Judge: </strong>{judgeName}
        <span style={{top: "inherit", bottom: "5px", backgroundColor: "white"}}
              className="ui-li-count">Total Runs / Aborts</span>
      </div>,
      <ul className="ui-listview ui-listview-inset ui-corner-all ui-shadow" key='ChalList'>
        {
          (challenges) ? challenges.map((item, num) => {
              const rkey = `${this.teamId}_${item.id}_`;
              return <Challenge
                compId={this.compId}
                divId={this.divId}
                teamId={this.teamId}
                key={item.id}
                runs={(this.props.runs[rkey + 'runs']) ? this.props.runs[rkey + 'runs'] : 0}
                aborts={(this.props.runs[rkey + 'aborts']) ? this.props.runs[rkey + 'aborts'] : 0}
                number={num} {...item} />
            }) :
            <li>No Data</li>
        }
      </ul>,
      <div className="ui-body ui-body-a ui-corner-all" key='Buttons'>
        <UIButton onClick={this.showTeamScoreClick}>
          Show Team Score
        </UIButton>
        <UIButton onClick={this.showNominatePopupClick}>
          Nominate Team
        </UIButton>
        <NominatePopup
          visible={this.state.nominateVisible}
          noms={noms}
          onCancel={this.hideNominatePopupClick}
          saveNoms={this.saveNoms}
        />
      </div>
    ]
  }
}

class Challenge extends Component {
  render() {
    return (
      <li>
        <Link
          to={`/c/${this.props.compId}/d/${this.props.divId}/t/${this.props.teamId}/h/${this.props.number}`}
          className="ui-btn ui-btn-icon-right ui-icon-carat-r">
          <div className="title-line">
            {this.props.number + 1}. {this.props.display_name}
          </div>
          <div className="second-line" style={{marginLeft: "1.2em"}}>
            {this.props.points} Points Possible
          </div>
          <span style={{backgroundColor: "white"}}
                className="ui-li-count">{this.props.runs} / {this.props.aborts}</span>
        </Link>
      </li>
    )
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    challengeData: state.challengeData,
    backURL: state.backURL,
    runs: state.runs,
    nominations: state.nominations
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    loadRuns: (teamId) => dispatch(loadRuns(teamId)),
    updateBack: (newURL) => dispatch(updateBackButton(newURL)),
    updateTitle: (newTitle) => dispatch(updatePageTitle(newTitle)),
    doLoadChalData: (year, level, data) => dispatch(loadChallengeData(year, level, data)),
    loadNominations: (teamId) => dispatch(loadNominations(teamId)),
    saveNominations: (teamId, values) => dispatch(saveNominations(teamId, values))
  }
}

const ChalList = connect(
  mapStateToProps,
  mapDispatchToProps
)(ChalListApp);

export default ChalList;