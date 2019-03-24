import  React, { Component} from 'react';
import { BrowserRouter , Route, Switch, Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { Button, DropdownButton, MenuItem } from 'react-bootstrap';

import InvoiceList from './InvoiceList'
import YearSelect from './YearSelect'
import {toggleShowAllTeams} from "../reducers/showAllTeams";
import {toggleShowAllVideos} from "../reducers/showAllVideos";
import {setInvoiceFilter} from "../reducers/filterInvoiceBy";

class MasterApp extends Component {
    constructor(props) {
        super(props);
        document.title = "Invoice Review";
        this.state = {
            settingsVisible: false
        }
    }

    componentDidUpdate(prevProps) {
        //document.title = this.props.title;
    }

    componentDidMount() {
    }

    componentWillUnmount() {
    }

    filterHandler(newMode, e) {
      e.preventDefault();
      this.props.setInvoiceFilter(newMode);
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
                  &nbsp;&nbsp;
                  <FilterByDropdown
                    selected={this.props.filterInvoiceBy}
                    onClick={(newMode,e) => this.filterHandler(newMode,e)}
                  />
                  <YearSelect />
                  <Switch>
                      <Route exact path="/:year?" component={InvoiceList} />
                  </Switch>
                </div>
            </BrowserRouter>

        )
    }
}

function FilterByDropdown(props) {
  const lookup = {
    "ALL": "Show All",
    "UNCHECKED_TEAMS": "Show Unchecked Teams",
    "UNCHECKED_VIDEOS": "Show Unchecked Videos"
  };

  return <DropdownButton
    id="FilterSelectDropdown"
    title={lookup[props.selected]}
    onSelect={props.onClick}
  >
    {
      Object.keys(lookup).map((val) => {
        return <MenuItem eventKey={val} key={val} active={val == props.selected}>{lookup[val]}</MenuItem>
      })
    }
  </DropdownButton>
}

// Map Redux state to component props
function mapStateToProps(state) {
    return {
      showAllTeams: state.showAllTeams,
      showAllVideos: state.showAllVideos,
      filterInvoiceBy: state.filterInvoiceBy,
    }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
    return {
      toggleShowAllTeams: () => dispatch(toggleShowAllTeams()),
      toggleShowAllVideos: () => dispatch(toggleShowAllVideos()),
      setInvoiceFilter: (mode) => dispatch(setInvoiceFilter(mode)),
    }
}

const Master =  connect(
    mapStateToProps,
    mapDispatchToProps
)(MasterApp);

export default Master;