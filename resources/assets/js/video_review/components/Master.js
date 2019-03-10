import  React, { Component} from 'react';
import { BrowserRouter , Route, Switch, Link } from 'react-router-dom';
import { connect } from 'react-redux';

import { Row, Col, Panel, Button } from 'react-bootstrap';

import YearSelect from './YearSelect';
import ReviewSelector from './ReviewSelector';
import VideoReview from './VideoReview';

class MasterApp extends Component {
    constructor(props) {
        super(props);
        this.state = {
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
            <BrowserRouter basename="video_review">
                <div>
                  <Row>
                    <Col xs={12}>
                      <YearSelect />
                    </Col>
                  </Row>
                  <Switch>
                      <Route exact path="/" component={PromptSelectYear} />
                      <Route exact path="/:year?" component={ReviewSelector} />
                      <Route exact path="/:year/:id" component={VideoReview}/>
                  </Switch>
                </div>
            </BrowserRouter>

        )
    }
}

class PromptSelectYear extends Component {
  render() {
    return <Row>
      <Col md={6} mdOffset={3}>
        <Panel>
          <Panel.Body>
            Select a Year
          </Panel.Body>
        </Panel>
      </Col>
    </Row>
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