
import React, {Component} from 'react';
import { Link } from 'react-router-dom';

export default class DivList extends Component {
    constructor(props) {
        super(props);
        const compId = props.match.params.compid;
        const divId = props.match.params.divid;
        const divisionList = props.myData[compId].divisions[divId];
        const teamList = divisionList.teams;
        this.state = {
            compid: compId,
            divisionName: divisionList.name,
            input: '',
            teams: Object.keys(teamList)
                .sort((a,b) => teamList[a].localeCompare(teamList[b], 'en', {'sensitivity': 'base'}))
                .reduce((list,teamId) => {
                list.push( {
                    key: teamId,
                    compid: compId,
                    divid: divId,
                    teanid: teamId,
                    name: teamList[teamId]
                });
                return list;
            },[])
        }
    }

    inputUpdate = (e) => { this.setState({input: e.target.value}) };
    clearInput = (e) => {
        this.setState({input: ''});
        document.getElementById('filter').value = "";
    };

    render() {
        let re = new RegExp('.*' + this.state.input + '.*',"gi");
        let list = this.state.teams.filter(item => item.name.match(re));
        return (
            <div>
                <h4>Division '{this.state.divisionName}' Teams</h4>
                <Link to={`/c/${this.state.compid}`}>Back to Divisions</Link><br />
                <div className="row">
                    <input id="filter" type={"text"} value={this.state.input} onChange={ this.inputUpdate } />
                    <button className="btn btn-xs btn-danger" onClick={ this.clearInput }>X</button>
                </div>
                <ul className="nav-list col-sm-4">
                    {
                        (list.length > 0) ? list.map(item => { return <Team {...item} />}) : <p>No Teams Found</p>
                    }
                </ul>
            </div>
        )
    }
}

class Team extends  Component {
    render() {
        return <li><Link to={`/c/${this.props.compid}/d/${this.props.divid}/t/${this.props.teanid}`} className="nav-item">{this.props.name}</Link></li>
    }
}