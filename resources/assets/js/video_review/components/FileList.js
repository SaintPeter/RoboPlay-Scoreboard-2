import React, {Component} from 'react';
import {connect} from 'react-redux';

import { Panel, ListGroup, ListGroupItem } from 'react-bootstrap';

class FileListApp extends Component {

  file_display(files) {
    if (Array.isArray(files) && files.length) {

      // Key files by cat
      const filesByCat = files.reduce((carry, file) => {
        if (!carry.hasOwnProperty(file.filetype.name)) {
          carry[file.filetype.name] = [];
        }
        carry[file.filetype.name].push(file);
        return carry;
      }, {});

      return Object.keys(filesByCat).map((cat) => {
          return <Panel.Heading>
              <Panel.Title toggle key={cat}>
                {cat} ({filesByCat[cat].length})
              </Panel.Title>
            </Panel.Heading>,
            <Panel.Collapse key={cat}>
              <ListGroup>
                {filesByCat[cat].map((file) => {
                  return <ListGroupItem key={file.id}>
                    {file.filename}
                    <span className="pull-right">
                        <i className={"fa " + file.filetype.icon}>{null}</i>
                      </span>
                  </ListGroupItem>
                })}
              </ListGroup>
            </Panel.Collapse>
      })
    } else {
      return null;
    }
  }

  render() {
    if(Array.isArray(this.props.files) && this.props.files.length) {
      return <Panel>
          {this.file_display(this.props.files)}
      </Panel>
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