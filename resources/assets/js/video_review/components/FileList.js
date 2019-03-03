import React, {Component} from 'react';
import {connect} from 'react-redux';

import {Panel, PanelGroup, ListGroup, ListGroupItem, Modal} from 'react-bootstrap';

class FileListApp extends Component {
  constructor(props) {
    super(props);
    if (Array.isArray(props.files)) {
      this.state = {
        filesByCat: props.files.reduce((carry, file) => {
          if (!carry.hasOwnProperty(file.filetype.name)) {
            carry[file.filetype.name] = [];
          }
          carry[file.filetype.name].push(file);
          return carry;
        }, {}),
      }
    } else {
      this.state = {
        filesByCat: {},
      }
    }
    this.state['showModal'] = false;
    this.state['showCat'] = '';
    this.state['showIndex'] = -1;
  }

  componentWillReceiveProps(newProps, oldProps) {
    if (newProps.files != oldProps.files) {
      this.setState({
        filesByCat: newProps.files.reduce((carry, file) => {
          if (!carry.hasOwnProperty(file.filetype.name)) {
            carry[file.filetype.name] = [];
          }
          carry[file.filetype.name].push(file);
          return carry;
        }, {})
      })
    }
  }

  showFile(e, cat, file_index) {
    e.preventDefault();
    e.nativeEvent.stopImmediatePropagation();

    console.log("Show file:", cat, file_index);
    this.setState({
      showModal: true,
      showCat: cat,
      showIndex: file_index
    });
  }

  file_display(files) {
    return Object.keys(this.state.filesByCat).map((cat, index) => {
      return <Panel eventKey={index} key={cat} defaultExpanded>
        <Panel.Heading>
          <Panel.Title toggle key={cat}>
            {cat} ({this.state.filesByCat[cat].length})
          </Panel.Title>
        </Panel.Heading>
        <Panel.Collapse key={cat}>
          <ListGroup>
            {this.state.filesByCat[cat].map((file, file_index) => {
              return <ListGroupItem key={file.id}>
                {
                  file.filetype.viewer == 'lytebox' ?
                    <a href={file.public_url} onClick={(e) => this.showFile(e, cat, file_index)}>
                      <i className={"fa " + file.filetype.icon}>{null}</i>
                      &nbsp;
                      {file.filename}
                    </a>
                    :
                    <span>
                    <i className={"fa " + file.filetype.icon}>{null}</i>
                      &nbsp;
                      {file.filename}
                    </span>
                }
                <span className="pull-right">
                       <a href={file.public_url} target="_blank">
                         <i className={"fa fa-arrow-circle-o-down"} title="Download File">{null}</i>
                       </a>
                  </span>
              </ListGroupItem>
            })}
          </ListGroup>
        </Panel.Collapse>
      </Panel>
    })
  }

  hideModal() {
    if (this.state.showModal) {
      this.setState({showModal: false});
    }
  }

  nextModal() {

  }


  ModalViewer(cat, index) {
    if (this.state.filesByCat.hasOwnProperty(cat) && this.state.filesByCat[cat].length - 1 >= index) {
      const file = this.state.filesByCat[cat][index];
      return <Modal bsSize="large" show={this.state.showModal} onHide={() => this.hideModal()}>
        <Modal.Header closeButton>{null}</Modal.Header>
        <Modal.Body>
          <div className="embed-responsive embed-responsive-4by3">
            <iframe src={file.public_url} className="embed-responsive-item"/>
          </div>
        </Modal.Body>
        <Modal.Footer>
          {file.filename}
        </Modal.Footer>
      </Modal>
    } else {
      return null;
    }

  }

  render() {
    if (Array.isArray(this.props.files) && this.props.files.length) {
      return [
        this.ModalViewer(this.state.showCat, this.state.showIndex),
        <PanelGroup id="1" xs={6}>
          {this.file_display()}
        </PanelGroup>]
    } else {
      return <Panel>
        <Panel.Heading>
          <h3>Files (0)</h3>
        </Panel.Heading>
        <Panel.Body>
          No Files Found
        </Panel.Body>
      </Panel>
    }
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

const FileList = connect(
  mapStateToProps,
  mapDispatchToProps
)(FileListApp);

export default FileList;