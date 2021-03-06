import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import {connect} from "react-redux";

import {updateBackButton, updatePageTitle} from "../actions/Generic";
import {loadChallengeData} from "../actions/ChallengeData";
import {deleteFavorite, saveFavorite} from "../actions/TeamList";


class TeamListApp extends Component {
  constructor(props) {
    super(props);
    this.compId = props.match.params.compId;
    this.divId = props.match.params.divId;
    this.year = compData[this.compId].year;

    const divisionList = compData[this.compId].divisions[this.divId];
    this.teamList = divisionList.teams;
    this.divisionName = divisionList.name;
    this.competitionName = compData[this.compId].name;
    this.level = divisionList.level;

    this.teams = Object.keys(this.teamList)
      .reduce((list, teamId) => {
        list.push({
          key: teamId,
          teamId: teamId,
          name: this.teamList[teamId]
        });
        return list;
      }, [])
      .sort(this.teamSort);

    this.state = {
      input: ''
    };
  }

  teamSort = (a, b) => {
    if ((this.props.teamFavorites[a.teamId] === this.props.teamFavorites[b.teamId])) {
      // If favorites are the same (set or unset), sort by name
      return a.name.localeCompare(b.name, 'en', {'sensitivity': 'base'})
    } else {
      // If they're not equal, then one is set and one is unset
      // If 'a' is set, we want it to be first in the list
      // Otherwise, not, compared to a non-favorite
      return (this.props.teamFavorites[a.teamId]) ? -1 : 1;
    }
  };

  componentDidMount() {
    this.props.updateTitle("Choose Team");
    this.props.updateBack(`/c/${this.compId}`, true);

    this.props.doLoadChalData(this.year, this.level);
  }

  inputUpdate = (e) => {
    e.preventDefault();
    let filterButton = document.getElementById('filter_button');
    if (e.target.value.length > 0) {
      filterButton.classList.remove('ui-input-clear-hidden');
    } else {
      if (!filterButton.classList.contains('ui-input-clear-hidden')) {
        filterButton.classList.add('ui-input-clear-hidden');
      }
    }
    this.setState({input: e.target.value})
  };

  clearInput = (e) => {
    this.setState({input: ''});
    document.getElementById('filter').value = "";
  };

  render() {
    let re = new RegExp('.*' + this.state.input + '.*', "gi");
    let list = this.teams.filter(item => item.name.match(re)).sort(this.teamSort);
    if (list.length) {
      list[0].firstItem = true;
      list[list.length - 1].lastItem = true;
    }
    return [
      <div className="ui-body ui-body-a ui-corner-all" key='TeamHeader'>
        <strong>Competition:</strong> {this.competitionName}<br/>
        <strong>Division:</strong> {this.divisionName}
      </div>,
      <div className="ui-filterable" key='SearchTeams'>
        <div className="ui-input-search ui-shadow-inset ui-input-has-clear ui-body-inherit ui-corner-all">
          <input id="filter" value={this.state.input} onChange={this.inputUpdate} placeholder="Search teams..."
                 data-lastval=""/>
          <a href="javascript:;"
             onClick={this.clearInput}
             className={"ui-input-clear ui-btn ui-icon-delete ui-btn-icon-notext ui-corner-all ui-input-clear-hidden"}
             title="Clear text"
             id="filter_button">
            Clear text
          </a>
        </div>
      </div>,
      <ul className="ui-listview ui-listview-inset ui-corner-all ui-shadow" key='TeamList'>
        {
          (list.length > 0) ? list.map(item => {
            return <Team
              compId={this.compId}
              divId={this.divId}
              saveFavorite={this.props.saveFavorite}
              deleteFavorite={this.props.deleteFavorite}
              isFavorite={(this.props.teamFavorites[item.teamId]) ? 1 : 0}
              {...item}
            />
          }) : <p>No Teams Found</p>
        }
      </ul>
    ]
  }
}

class Team extends Component {
  doToggleFavorite = () => {
    if (this.props.isFavorite) {
      this.props.deleteFavorite(this.props.teamId);
    } else {
      this.props.saveFavorite(this.props.teamId);
    }
  };

  render() {
    let childType = this.props.firstItem ? 'ui-first-child' : '';
    childType += this.props.lastItem ? 'ui-last-child' : '';

    let match = this.props.name.match(/^(.+?)\s*\(([^\)]+)\)/);
    let teamName = match[1];
    let schoolName = match[2];

    return (
      <li className={childType}>
        <Link
          to={`/c/${this.props.compId}/d/${this.props.divId}/t/${this.props.teamId}`}
          className='ui-btn ui-btn-icon-right ui-icon-carat-r'>
          <div className="title-line">
            {teamName}
          </div>
          <div className="second-line">
            {schoolName}
          </div>
        </Link>
        <button onClick={this.doToggleFavorite}
                title={(this.props.isFavorite) ? 'Remove from Favorites' : 'Add to Favorites'}
                className="favoriteButton"
        >
          <i
            className={this.props.isFavorite ? "favoriteStar-active fa fa-star" : "favoriteStar-inactive fa fa-star-o"}></i>
        </button>
      </li>
    )
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    challengeData: state.challengeData,
    teamFavorites: state.teamList.teamFavorites
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    updateBack: (newURL, show) => dispatch(updateBackButton(newURL, show)),
    updateTitle: (newTitle) => dispatch(updatePageTitle(newTitle)),
    doLoadChalData: (year, level, data) => dispatch(loadChallengeData(year, level, data)),
    saveFavorite: (id) => dispatch(saveFavorite(id)),
    deleteFavorite: (id) => dispatch(deleteFavorite(id))
  }
}

const TeamList = connect(
  mapStateToProps,
  mapDispatchToProps
)(TeamListApp);

export default TeamList;