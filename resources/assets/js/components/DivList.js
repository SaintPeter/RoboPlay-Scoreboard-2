import React, {Component} from 'react';
import { Link } from 'react-router-dom';

export default class TeamList extends Component {
    constructor(props) {
        super(props);
        const compId = props.match.params.compid;
        this.state = {
            competitionName: compData[compId].name,
            divs: Object.keys(compData[compId].divisions).reduce((list,divId) => {
                list.push( {
                    key: divId,
                    compid: compId,
                    divid: divId,
                    name: compData[compId].divisions[divId].name
                });
                return list;
            },[])
        }
    }

    componentDidMount() {
        document.getElementById('header').innerHTML = "Choose Division";
        this.props.updateBack('/');
    }

    render() {
        return (
            <div className="ui-content">
                <h4>Competition: {this.state.competitionName}</h4>
                <ul className="ui-listview listview-spacer">
                    {
                        this.state.divs.map(item => {
                            return <Division {...item} />})
                    }
                </ul>
            </div>
        )
    }
}

class Division extends  Component {
    render() {
        return <li><Link to={`/c/${this.props.compid}/d/${this.props.divid}`} className="ui-btn ui-btn-icon-right ui-icon-carat-r">{this.props.name}</Link></li>
    }
}