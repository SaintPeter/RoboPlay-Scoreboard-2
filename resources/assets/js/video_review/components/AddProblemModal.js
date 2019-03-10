import React, {Component} from 'react';
import {connect} from 'react-redux';

import { Alert, Modal, Button, FormGroup, FormControl, ControlLabel, Checkbox } from 'react-bootstrap';
import { formatTimestamp } from "../utils";

class AddProblemModalApp extends Component {

  constructor(props) {
    super(props);

    this.state = {
      problemData: {
        comment: '',
        video_review_details_id: 0,
        timestamp: -1
      },
      saveTimestamp: false
    };

    this.interval = null;
  }

  componentWillReceiveProps(nextProps) {
    // Reset Save Timestamp on show
    if(this.props.show != nextProps.show && nextProps.show) {
      this.setState({
        error: false,
        message: '',
        saveTimestamp: false,
        problemData: {
          comment: '',
          video_review_details_id: 0,
          timestamp: -1
        }
      });
    }
  }

  saveProblem() {
    if(this.state.problemData.hasOwnProperty("video_review_details_id") &&
      problemDetailList.hasOwnProperty(this.state.problemData.video_review_details_id))
    {
      console.log("Problem Data:", this.state.problemData, " Save Timestamp: ", this.state.saveTimestamp);
      this.props.addProblemHandler(this.state.problemData, this.state.saveTimestamp);
      this.props.hideHandler();
    } else {
      this.setState({
        error: true,
        message: "You must select a problem!"
      })
    }
  }

  timestampToggle(e) {
    this.setState({saveTimestamp: !!e.target.value});
  }

  changeHandler(e, fieldName) {
    this.setState({
      problemData: Object.assign({}, this.state.problemData, {
        [fieldName]: e.target.value
      })
    })
  }

  problemList() {
    return [
      <option value={-1} key={-1}>-- Select Problem --</option>,
      problemList.map((cat, index) => {
      return <optgroup label={cat.name} key={cat.id}>
        { problemList[index].details.map(detail => {
            return <option value={detail.id} key={detail.id}>{detail.reason}</option>
        })}
      </optgroup>
    })]
  }

  render() {
    return <Modal show={this.props.show} onHide={(e) => this.props.hideHandler(e)}>
      <Modal.Header>
        <Modal.Title>
          Add Problem
        </Modal.Title>
      </Modal.Header>
      <Modal.Body>
        {
          (this.state.error) ?
          <Alert bsStyle="warning">
            {this.state.message}
          </Alert>
          :
            null
        }
        <form>
          <FormGroup>
            <ControlLabel>Type</ControlLabel>
            <FormControl componentClass="select" onChange={(e) => this.changeHandler(e, 'video_review_details_id')}>
              {this.problemList()}
            </FormControl>
          </FormGroup>
          <FormGroup>
            <Checkbox onChange={e => this.timestampToggle(e)} value={true}>
              Save Timestamp &mdash; <span>{formatTimestamp(this.props.timestamp)}</span>
            </Checkbox>
          </FormGroup>
          <FormGroup>
            <ControlLabel>Comment</ControlLabel>
            <FormControl componentClass="textarea" onChange={(e) => this.changeHandler(e, 'comment')} />
          </FormGroup>
        </form>
      </Modal.Body>
      <Modal.Footer>
        <Button bsStyle="info" onClick={(e) => this.props.hideHandler(e)}>Cancel</Button>
        <Button bsStyle="primary" onClick={() => this.saveProblem()}>Save</Button>
      </Modal.Footer>
    </Modal>
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {}
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {}
}

const AddProblemModal = connect(
  mapStateToProps,
  mapDispatchToProps
)(AddProblemModalApp);

export default AddProblemModal;