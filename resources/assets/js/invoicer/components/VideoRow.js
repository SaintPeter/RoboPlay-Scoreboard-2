import React, {Component} from 'react';
import {connect} from 'react-redux';

import {updateVideoDivision} from "../reducers/invoiceData"

class VideoRowApp extends Component {
  videoDivisonChangeHandler = (e, videoId) => {
    console.log(`Update - Invoice: ${this.props.invoiceId} Video: ${videoId} changed to ${e.target.value}`);
    this.props.updateVideoDivision(this.props.invoiceId,videoId,e.target.value);
  };

  render() {
    const videos = this.props.rowData;
    let divData = this.props.divData;

    if(videos && videos.length > 0 && (this.props.showAllVideos || this.props.showVideosList.hasOwnProperty(this.props.invoiceId))) {
      return <tr key={"invoice_" + this.props.invoiceId}>
        <td colSpan={8}>
          <table className="table">
            <thead>
              <tr>
                <th>Video</th>
                <th>Division</th>
                <th colSpan="3">Validation</th>
                <th className="text-center">Students</th>
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
              <td className="text-center" colSpan="3">
                    <span className={video.status_class}>
                      {video.status}
                    </span>
              </td>
              <td className="text-center">{video.student_count}</td>
              <td>
                Status Button Here
              </td>

            </tr>,
            <tr  key={"video_notes_" + video.id}>
              <td colSpan="8" className="video_notes_section">
                <label>Notes</label>
                <textarea className="video_notes" id="video_notes{{ $video->id }}"
                          data-id="{{ $video->id }}" defaultValue={video.notes} />
              </td>
            </tr> ]
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
    updateVideoDivision: (invoiceId,videoId,newDivsion) => dispatch(updateVideoDivision(invoiceId,videoId,newDivsion))
  }
}

const VideoRow = connect(
  mapStateToProps,
  mapDispatchToProps
)(VideoRowApp);

export default VideoRow;