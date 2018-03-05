import React, {Component} from 'react';
import { BrowserRouter , Route, Switch, Link } from 'react-router-dom';
//import { redux } from 'redux-react'

import CompList from "./CompList";
import DivList from "./DivList";
import TeamList from "./TeamList";

// import loadCompData from "../actions/loadCompData";

class Master extends Component {
    constructor(props) {
        super(props);
        this.state = {
            backURL: ""
        };
    }

    componentWillMount() {
        document.title = "Select Competition";
    }

    updateBackButton = (newURL) => { this.setState({'backURL': newURL})};

    render(){
        return (
            <BrowserRouter basename="scorer">
                <div className="ui-page ui-page-theme-a ui-page-active">
                    <div className="ui-header ui-bar-inherit">
                        <h1 id="header" className="ui-title">Competition List</h1>
                        <a href="/" className="ui-btn-left ui-link ui-btn ui-icon-home ui-btn-icon-notext ui-shadow ui-corner-all" data-icon="home" data-ajax="false" data-iconpos="notext" data-direction="reverse">Home</a>
                        <Link to={this.state.backURL} id="back_button" className="ui-btn-right ui-link ui-btn ui-icon-back ui-btn-icon-notext ui-shadow ui-corner-all">Back</Link>
                    </div>

                    <Switch>
                        <Route exact path="/" render={(props) => <CompList {...props} updateBack={this.updateBackButton} /> } />
                        <Route exact path="/c/:compid" render={({match}) => <DivList match={match} updateBack={this.updateBackButton} /> } />
                        <Route exact path="/c/:compid/d/:divid" render={({match}) => <TeamList match={match} updateBack={this.updateBackButton}/> } />
                    </Switch>


                    <div className="ui-footer ui-bar-inherit">
                        <h4>
                            <div style={{ textAlign: 'center'}}>
                                <span style={{ fontSize: 10 }}>blah</span>
                            </div>
                        </h4>

                    </div>
                </div>
            </BrowserRouter>

        )
    }
}
export default Master;