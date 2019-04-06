import React, {Component} from 'react';
import {connect} from 'react-redux';

import { Panel, ListGroup, ListGroupItem, Button, Row, Col} from 'react-bootstrap';
import { formatTimestamp, lookupDetailType, formatTime } from "../utils";

class ProblemListApp extends Component {


    render() {
      const problemHeader =
        <Panel.Heading key={"problem_header"}>
          <Panel.Title>
            Problems
            <span className="pull-right">
              <Button bsSize="xsmall" onClick={(e) => this.props.showProblemModal(e)} title="Add Problem">
                <i className="fa fa-plus">{null}</i>
                &nbsp;Add
              </Button>
            </span>
          </Panel.Title>
        </Panel.Heading>;

      const stats = this.props.problems.reduce((carry, problem) => {
        carry['written'] += problem.written ? 1 : 0;
        carry['resolved'] += problem.hasOwnProperty('resolved') && problem.resolved ? 1 : 0;
        carry['unresolved'] += (problem.hasOwnProperty('resolved') && !problem.resolved ) ? 1 : 0;
        return carry;
      },{ written: 0, resolved: 0, unresolved: 0});

      let actions = null;
      if(stats.written != this.props.problems.length) {
        if(stats.resolved == this.props.problems.length) {
          if(this.props.video.review_status != 1) {
            actions = <Button bsStyle="success" onClick={this.props.saveHandler}>Save No Problems Found</Button>
          }
        } else {
          actions = <Button bsStyle="warning" onClick={this.props.saveHandler}>Save Problems</Button>
        }
      }

      if(this.props.problems.length) {
        return <Panel>
          {problemHeader}
          <DetailList
            problems={this.props.problems}
            deleteHandler={this.props.deleteHandler}
            editHandler={this.props.editHandler}
            changeTimeHandler={this.props.changeTimeHandler}
            resolveProblemHandler={this.props.resolveProblemHandler}
          />
          <Panel.Footer key={"problems_footer"} className="text-right">
            <Button bsStyle="info" onClick={this.props.cancelHandler}>Cancel</Button>
            &nbsp;
            {actions}
          </Panel.Footer>
        </Panel>
      } else {
        return <Panel>
          {problemHeader}
          <Panel.Body key={"problems_body"}>
            No Problems Yet
          </Panel.Body>
          <Panel.Footer key={"problems_footer"} className="text-right">
            <Button bsStyle="info" onClick={this.props.cancelHandler}>Cancel</Button>
            &nbsp;
            { (stats.resolved + stats.unresolved) === 0 ?
              <Button bsStyle="success" onClick={this.props.saveHandler}>Move to Passed</Button> : null
            }
          </Panel.Footer>
        </Panel>
      }
  }
}

const DetailList = (props) => {
  return <ListGroup>
    { props.problems.map((problem, index) => {
      let actionCol = null;

      if(problem.written) {
        if(!problem.resolved) {
          actionCol =
            <Col xs={2} className="btn" onClick={(e) => props.resolveProblemHandler(e, index)} title="Mark Issue Resolved">
              <i className="fa fa-check fa-2x" style={{color: 'green'}}>{null}</i>
            </Col>;
        }
      } else {
        if (!problem.hasOwnProperty('id')) {
          actionCol =
            <Col xs={2} className="btn" onClick={(e) => props.deleteHandler(e, index)} title="Delete Unsaved Issue">
              <i className="fa fa-times fa-2x" style={{color: 'red'}}>{null}</i>
            </Col>;
        }
      }

      let itemStyle  = null;
      if(!problem.written) {
        itemStyle = 'warning'
      }
      if(problem.resolved) {
        itemStyle = 'success'
      }

      return <ListGroupItem
        bsStyle={itemStyle}
        key={index}
        onClick={ !problem.resolved ? () => props.editHandler(index) : null }
      >
        <Row>
          <Col xs={10}>
            <h4>
              { !problem.written ? <span><i className="fa fa-asterisk">{null}</i>&nbsp;</span>: null }
              {lookupDetailType(problem)}
              { problem.resolved ? ' (Resolved)' : null}
              {formatTime(problem, props.changeTimeHandler)}
            </h4>
            {problem.comment}
          </Col>
          {actionCol}
        </Row>
      </ListGroupItem>
    })}
  </ListGroup>
};

// Map Redux state to component props
function mapStateToProps(state) {
  return {}
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {}
}

const ProblemList = connect(
  mapStateToProps,
  mapDispatchToProps
)(ProblemListApp);

export default ProblemList;