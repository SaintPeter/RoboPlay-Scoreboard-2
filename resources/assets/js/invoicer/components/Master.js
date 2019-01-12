import  React, { Component} from 'react';
import { BrowserRouter , Route, Switch, Link } from 'react-router-dom';
import { connect } from 'react-redux';

import InvoiceList from './InvoiceList'

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
                <div className="content">

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
    }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
    return {
    }
}

const Master =  connect(
    mapStateToProps,
    mapDispatchToProps
)(MasterApp);

export default Master;