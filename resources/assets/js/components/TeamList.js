
import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import {connect} from "react-redux";
import loadChalData from "../utils/loadChalData";

class TeamListApp extends Component {
    constructor(props) {
        super(props);
        const compId = props.match.params.compId;
        const divId = props.match.params.divId;
        const divisionList = compData[compId].divisions[divId];
        const teamList = divisionList.teams;
        this.state = {
            compId: compId,
            divId: divId,
            year: compData[compId].year,
            level: compData[compId].divisions[divId].level,
            divisionName: divisionList.name,
            input: '',
            teams: Object.keys(teamList)
                .sort((a,b) => teamList[a].localeCompare(teamList[b], 'en', {'sensitivity': 'base'})) // TODO: Add Favorites
                .reduce((list,teamId) => {
                list.push( {
                    key: teamId,
                    teamId: teamId,
                    name: teamList[teamId]
                });
                return list;
            },[])
        };
    }

    componentDidMount() {
        this.props.updateTitle("Choose Team");
        this.props.updateBack(`/c/${this.state.compId}`);

        if(!this.props.challengeData[this.state.year] || !this.props.challengeData[this.state.year][this.state.level]) {
            loadChalData.load(this.state.year, this.state.level, this.props.doLoadChalData)
        } else {
            console.log("TeamList - No need to load Challenge Data");
        }
    }

    inputUpdate = (e) => {
        e.preventDefault();
        let filterButton = document.getElementById('filter_button');
        if(e.target.value.length > 0) {
            filterButton.classList.remove('ui-input-clear-hidden');
        } else {
            if(!filterButton.classList.contains('ui-input-clear-hidden')) {
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
        let re = new RegExp('.*' + this.state.input + '.*',"gi");
        let list = this.state.teams.filter(item => item.name.match(re));
        if(list.length) {
            list[0].firstItem = true;
            list[list.length - 1].lastItem = true;
        }
        return (
            <div className="ui-content">
                <h4>{this.state.divisionName} Teams</h4>

                <div className="ui-filterable">
                    <div className="ui-input-search ui-shadow-inset ui-input-has-clear ui-body-inherit ui-corner-all">
                        <input id="filter" value={this.state.input} onChange={ this.inputUpdate } placeholder="Search teams..." data-lastval="" />
                        <a href="javascript:;"
                           onClick={ this.clearInput }
                           className={ "ui-input-clear ui-btn ui-icon-delete ui-btn-icon-notext ui-corner-all ui-input-clear-hidden" }
                           title="Clear text"
                           id="filter_button">
                            Clear text
                        </a>
                    </div>
                </div>
                <ul className="ui-listview ui-listview-inset ui-corner-all ui-shadow">
                    {
                        (list.length > 0) ? list.map(item => {
                            return <Team
                                compId={this.state.compId}
                                divId={this.state.divId}
                                {...item}
                            />}) : <p>No Teams Found</p>
                    }
                </ul>
            </div>
        )
    }
}

class Team extends  Component {
    render() {
        let childType = this.props.firstItem ? 'ui-first-child' : '';
        childType += this.props.lastItem ? 'ui-last-child' : '';

        return (
            <li className={childType}>
                <Link
                    to={`/c/${this.props.compId}/d/${this.props.divId}/t/${this.props.teamId}`}
                    className='ui-btn ui-btn-icon-right ui-icon-carat-r'>
                    {this.props.name}
                </Link>
            </li>
        )
    }
}

// Map Redux state to component props
function mapStateToProps(state) {
    return {
        challengeData: state.challengeData,
        backURL: state.backURL
    }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
    return {
        updateBack: (newURL) => dispatch({ type: 'change_url', url: newURL}),
        updateTitle: (newTitle) => dispatch({ type: 'change_title', title: newTitle }),
        doLoadChalData: (year,level,data) =>dispatch({ type: 'load_chal_data', 'year': year, 'level': level, 'data': data})
    }
}

const TeamList =  connect(
    mapStateToProps,
    mapDispatchToProps
)(TeamListApp);

export default TeamList;