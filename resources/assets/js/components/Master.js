import React, {Component} from 'react';
import { BrowserRouter , Route, Switch } from 'react-router-dom';

import CompList from "./CompList";
import DivList from "./DivList";
import TeamList from "./TeamList";

import loadCompData from "../actions/loadCompData";

class Master extends Component {
    constructor(props) {
        super(props);
        this.state = {
            myData: {}
        };
    }

    componentWillMount() {
        loadCompData.load(this,'myData');
    }

    render(){
        return (
            <div className="container">
                <BrowserRouter basename="react">
                    <Switch>
                        <Route exact path="/" render={(props) => <CompList myData={this.state.myData} {...props} /> } />
                        <Route exact path="/c/:compid" render={({match}) => <DivList myData={this.state.myData} match={match} /> } />
                        <Route exact path="/c/:compid/d/:divid" render={({match}) => <TeamList myData={this.state.myData} match={match} /> } />
                    </Switch>
                </BrowserRouter>
            </div>
        )
    }
}
export default Master;