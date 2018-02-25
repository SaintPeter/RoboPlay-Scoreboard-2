import React, {Component} from 'react';
import { Link } from 'react-router-dom';

export default class CompList extends Component {
    constructor(props) {
        super(props);
        this.state = this.readData(props);
    }

    componentWillReceiveProps(nextProps) {
        this.setState(this.readData(nextProps));
    }

    readData(props) {
        let newState = {comps: []};

        newState.comps = Object.keys(props.myData).reduce((list, compId) => {
            list.push({
                key: compId,
                id: compId,
                name: props.myData[compId].name
            });
            return list;
        }, []);

        return newState;
    }

    render() {
        return (
            <div>
                <h4>Competition List</h4>
                <ul>
                    {
                        this.state.comps.map(item => {
                            return <Comp {...item} />})
                    }
                </ul>
            </div>
        )
    }
}

class Comp extends  Component {
    render() {
        return <li><Link to={`/c/${this.props.id}`}>{this.props.name}</Link></li>
    }
}