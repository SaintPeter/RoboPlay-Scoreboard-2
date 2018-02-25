import React, {Component} from 'react';
import { Link } from 'react-router-dom';

export default class TeamList extends Component {
    constructor(props) {
        super(props);
        const compId = props.match.params.compid;
        this.state = {
            competitionName: props.myData[compId].name,
            divs: Object.keys(props.myData[compId].divisions).reduce((list,divId) => {
                list.push( {
                    key: divId,
                    compid: compId,
                    divid: divId,
                    name: props.myData[compId].divisions[divId].name
                });
                return list;
            },[])
        }
    }

    render() {
        return (
            <div>
                <h4>Competition: {this.state.competitionName}</h4>
                <Link to={'/'}>Back to Competitions</Link>
                <ul>
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
        return <li><Link to={`/c/${this.props.compid}/d/${this.props.divid}`}>{this.props.name}</Link></li>
    }
}