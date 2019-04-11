import React, {Component} from 'react';
import {connect} from 'react-redux';

import {updateVideoDivision,updateVideoChecked, updateVideoNotes} from "../reducers/invoiceData"

class VideoRowApp extends Component {
  videoDivisonChangeHandler = (e, videoId) => {
    console.log(`Update - Invoice: ${this.props.invoiceId} Video: ${videoId} changed to ${e.target.value}`);
    this.props.updateVideoDivision(this.props.invoiceId,videoId,e.target.value);
  };

  handleVideoCheckedChanged = (videoId) => {
    console.log(`Update Checked - Invoice: ${this.props.invoiceId} Video: ${videoId} Toggle`);
    this.props.updateVideoChecked(this.props.invoiceId, videoId);
  };

  render() {
    const videos = this.props.rowData;
    let divData = this.props.divData;

    if(videos && videos.length > 0 && (this.props.showAllVideos || this.props.showVideosList.hasOwnProperty(this.props.invoiceId))) {
      return <tr key={"invoice_" + this.props.invoiceId}>
        <td colSpan={7}>
          <table className="table">
            <thead>
              <tr>
                <th>Video</th>
                <th>Division</th>
                <th className="text-center">Students</th>
                <th colSpan="3">Validation</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
            {videos.map( video => {
            return [ <tr key={"video_" + video.id}>
              <td>
                {video.name}
                (<a href={"http://youtube.com/watch?v="+ video.yt_code} target="_new">YouTube</a>)
              </td>
              <td>
                <VideoDropDown
                  divData={divData}
                  onChange={(e) => this.videoDivisonChangeHandler(e,video.id)}
                  value={video.vid_division_id}/>
              </td>
              <td className="text-center">{video.student_count}</td>
              <td className="text-center" colSpan="3">
                {video.review_status}
              </td>
              <td>
                <CheckedButton
                  onClick={() => this.handleVideoCheckedChanged(video.id)}
                  status={video.status}
                />
              </td>

            </tr>,
            <VideoNotes
              key={"video_notes_" + video.id}
              onSave={this.props.updateVideoNotes}
              video={video}
              invoiceId={this.props.invoiceId}
            />
            ]
            })}
            </tbody>
          </table>
        </td>
      </tr>
    } else {
      return (null) ;
    }

  }
}

class VideoNotes extends Component {
  constructor(props) {
    super(props);
    this.state = {
      'value': props.video.notes
    };
    this.timerHandle = '';
  }

  inputHandler(e) {
    this.setState({'value': e.target.value});
    clearTimeout(this.timerHandle);
    this.timerHandle = setTimeout((_this) => {
      // Only write a change if an actual change occured
      if(_this.props.video.notes != _this.state.value) {
        _this.props.onSave(_this.props.invoiceId, _this.props.video.id, _this.state.value)
      }
    }, 1500, this)
  }

  render() {
    let writeClass = this.props.video.writing ? " writing_color" : "";
    return <tr>
      <td colSpan="7" className="video_notes_section">
        <label>Notes</label>
        <textarea style={{'clear':'both', 'width':'100%'}}
                  className={"animate_color" + writeClass}
                  defaultValue={this.state.value}
                  onInput={(e) => this.inputHandler(e)}
        />
      </td>
    </tr>
  }
}

class VideoDropDown extends Component {
  render() {
    return <select onChange={this.props.onChange} value={this.props.value}>
      {
        Object.entries(this.props.divData).map(items => {
            return <option key={items[0]} value={items[0]}>{items[1]}</option>
          })
      }
    </select>
  }
}

class CheckedButton extends Component {
  render() {
    if(this.props.status) {
      return <button
        className={"btn btn-success btn-sm team_audit_button"}
        onClick={this.props.onClick}
        title={"Click to mark Unchecked"}
      >Checked
      </button>
    } else {
      return <button
        className={"btn btn-danger btn-sm team_audit_button"}
        onClick={this.props.onClick}
        title={"Click to mark Checked"}
      >Unchecked
      </button>
    }
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    showVideosList: state.showVideosList,
    showAllVideos: state.showAllVideos
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    updateVideoDivision: (invoiceId,videoId,newDivsion) => dispatch(updateVideoDivision(invoiceId,videoId,newDivsion)),
    updateVideoChecked: (invoiceId, videoId) => dispatch(updateVideoChecked(invoiceId,videoId)),
    updateVideoNotes: (invoiceId, videoId, notes) => dispatch(updateVideoNotes(invoiceId, videoId, notes)),
  }
}

const VideoRow = connect(
  mapStateToProps,
  mapDispatchToProps
)(VideoRowApp);

export default VideoRow;