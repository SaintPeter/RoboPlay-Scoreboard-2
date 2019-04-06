import React, {Component} from 'react';
import { Modal, Button } from "react-bootstrap";

export default class ReviewDqModal extends Component {
  constructor(props) {
    super(props);

    this.state = {
      loading: true,
    }
  }

  clearLoading = (e) => {
    this.setState({loading: false})
  };

  componentWillReceiveProps(nextProps, nextContext) {
    if(!this.props.visible && nextProps.visible) {
      this.setState({loading: true});
    }
  }

  render() {
    return <Modal bsSize="large" onHide={this.props.onHide} show={this.props.visible}>
      <Modal.Header>
        <Modal.Title>
          Review Disqualification Email
        </Modal.Title>
      </Modal.Header>
      <Modal.Body>

        <div className="embed-responsive embed-responsive-4by3">
          <span style={{display: this.state.loading ? 'block' : 'none'}}>
            <h2 className="text-center">
              Loading . . .<br />
              <i className="fa fa-spinner fa-pulse fa-fw">{null}</i>
            </h2>
          </span>
          { this.props.visible ?
            <iframe
              onLoad={this.clearLoading}
              src={`/api/video_review/dq_preview/${this.props.video.id}`}
              className="embed-responsive-item"
            />
            :
            null
          }

        </div>
      </Modal.Body>
      <Modal.Footer>
        <Button bsStyle="info" onClick={this.props.onHide}>Cancel</Button>
        <Button bsStyle="primary" onClick={this.props.onSave}>Send</Button>
      </Modal.Footer>
    </Modal>
  }
}

