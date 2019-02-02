import  React, { Component} from 'react';
import { BrowserRouter , Route, Switch, Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { Button } from 'react-bootstrap';

import InvoiceList from './InvoiceList'
import YearSelect from './YearSelect'
import {toggleShowAllTeams} from "../reducers/showAllTeams";
import {toggleShowAllVideos} from "../reducers/showAllVideos";

class MasterApp extends Component {
    constructor(props) {
        super(props);
        document.title = "Invoice Review";
        this.state = {
            settingsVisible: false
        }
    }

    componentDidUpdate(prevProps) {
        document.title = this.props.title;
    }

    componentDidMount() {
    }

    componentWillUnmount() {
    }

    render(){
        return (
            <BrowserRouter basename="invoicer">
                <div>
                  <Button bsStyle="info" onClick={this.props.toggleShowAllTeams}>
                    { this.props.showAllTeams ? 'Hide Teams' : 'Show Teams'}
                  </Button>
                  &nbsp;&nbsp;
                  <Button bsStyle="success" onClick={this.props.toggleShowAllVideos}>
                    { this.props.showAllVideos ? 'Hide Videos' : 'Show Videos'}
                  </Button>
                  <YearSelect />
                  <Switch>
                      <Route exact path="/:year?" component={InvoiceList} />
                  </Switch>
                </div>
            </BrowserRouter>

        )
    }
}

// Map Redux state to component props
function mapStateToProps(state) {
    return {
      showAllTeams: state.showAllTeams,
      showAllVideos: state.showAllVideos
    }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
    return {
      toggleShowAllTeams: () => dispatch(toggleShowAllTeams()),
      toggleShowAllVideos: () => dispatch(toggleShowAllVideos())
    }
}

const Master =  connect(
    mapStateToProps,
    mapDispatchToProps
)(MasterApp);

export default Master;