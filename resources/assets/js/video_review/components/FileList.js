import React, {Component} from 'react';
import {connect} from 'react-redux';

import {Panel, PanelGroup, ListGroup, ListGroupItem, Modal} from 'react-bootstrap';

class FileListApp extends Component {
  constructor(props) {
    super(props);
    if (Array.isArray(props.files)) {
      let filesByCat = this.createCatFileList(props.files);

      this.state = {
        filesByCat: filesByCat,
        lightboxFiles: this.createLightboxFileList(filesByCat)
      }
    } else {
      this.state = {
        filesByCat: {},
        lightboxFiles: []
      }
    }
    this.state['showModal'] = false;
    this.state['showIndex'] = 0;
  }

  componentWillReceiveProps(newProps, oldProps) {
    if (newProps.files != oldProps.files) {
      let filesByCat = this.createCatFileList(newProps.files);

      this.setState({
        filesByCat: filesByCat,
        lightboxFiles: this.createLightboxFileList(filesByCat)
      })
    }
  }

  createCatFileList(files) {
    return files.reduce((carry, file) => {
      if (!carry.hasOwnProperty(file.filetype.name)) {
        carry[file.filetype.name] = [];
      }
      carry[file.filetype.name].push(file);
      return carry;
    }, {})
  }

  createLightboxFileList(catFiles) {
    let index = 0;
    return Object.keys(catFiles).reduce((catCarry, cat) => {
      catFiles[cat].forEach((file) => {
        if (file.filetype.viewer == 'lytebox') {
          file.index = index;
          catCarry.push(file);
          index++;
        }
      });
      return catCarry;
    }, []);
  }

  showFile(e, file_index) {
    e.preventDefault();
    e.nativeEvent.stopImmediatePropagation();

    console.log("Show file:", file_index);
    this.setState({
      showModal: true,
      showIndex: file_index
    });
  }

  file_display(files) {
    return Object.keys(this.state.filesByCat).map((cat, index) => {
      return <Panel eventKey={index} key={cat} defaultExpanded>
        <Panel.Heading key={cat + '_heading'}>
          <Panel.Title toggle key={cat}>
            {cat} ({this.state.filesByCat[cat].length})
          </Panel.Title>
        </Panel.Heading>
        <Panel.Collapse key={cat + '_collapse'}>
          <ListGroup>
            {this.state.filesByCat[cat].map((file) => {
              return <ListGroupItem key={file.id}>
                {
                  file.filetype.viewer == 'lytebox' ?
                    <a href={file.public_url} onClick={(e) => this.showFile(e, file.index)} key={file.id}>
                      <i className={"fa " + file.filetype.icon}>{null}</i>
                      &nbsp;
                      {file.filename}
                    </a>
                    :
                    <span key={file.id}>
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
    if (this.state.showIndex < this.state.lightboxFiles.length - 1) {
      this.setState({showIndex: this.state.showIndex + 1});
    } else {
      // Wrap around
      this.setState({showIndex: 0});
    }
  }

  prevModal() {
    if (this.state.showIndex > 0) {
      this.setState({showIndex: this.state.showIndex - 1});
    } else {
      // Wrap around
      this.setState({showIndex: this.state.lightboxFiles.length - 1});
    }
  }


  ModalViewer(selIndex) {
    if (this.state.lightboxFiles.length > 0) {
      return <Modal bsSize="large" show={this.state.showModal} onHide={() => this.hideModal()} key={"somekey"}>
        <Modal.Header closeButton>
          <Modal.Title>
            {this.state.lightboxFiles[selIndex].filename}
          </Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <div className="embed-responsive embed-responsive-4by3">
            {this.state.lightboxFiles.map((file, index) => {
              return this.embedFile(file, index == selIndex);

            })}
          </div>
        </Modal.Body>
        <Modal.Footer>
          <div className="text-center">
            <span className="pull-left" onClick={() => this.prevModal()}>
              <i className="fa fa-arrow-left fa-2x">{null}</i>
            </span>
            { selIndex + 1 } / { this.state.lightboxFiles.length }
            <span className="pull-right" onClick={() => this.nextModal()}>
              <i className="fa fa-arrow-right fa-2x">{null}</i>
            </span>
          </div>
        </Modal.Footer>
      </Modal>
    } else {
      return null;
    }

  }

  embedFile(file, show) {
    if(file.filetype.type != 'video') {
      return <iframe
        key={file.id}
        src={file.public_url}
        className={"embed-responsive-item" + (show ? '' : ' hidden')}
      />
    } else {
      return <video key={file.id}
        controls={true}
        className={"embed-responsive-item" + (show ? '' : ' hidden')}
      >
        <source src={file.public_url} />
      </video>
    }
  }

  render() {
    if (Array.isArray(this.props.files) && this.props.files.length) {
      return [
        this.ModalViewer(this.state.showIndex),
        <PanelGroup id="1" md={6} key={"some_panel_group"}>
          {this.file_display()}
        </PanelGroup>]
    } else {
      return <Panel key={"empty_panel"}>
        <Panel.Body key={"empty_panel_body"}>
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