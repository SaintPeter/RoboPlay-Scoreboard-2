import React, {Component} from 'react';
import {connect} from 'react-redux';

class VideoRowApp extends Component {
  render() {
    const videos = this.props.rowData.video_data;

    if(videos && videos.length > 0 && this.props.visible) {
      return <tr key={this.props.id}>
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
                Video Division Selector Here
              </td>
              <td className="text-center" colSpan="3">
                    <span className={video.status_class}>
                      {video.status}
                    </span>
              </td>
              <td className="text-center">{video.student_count}</td>
              <td>
                Video Button Here
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

// Map Redux state to component props
function mapStateToProps(state) {
  return {}
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {}
}

const VideoRow = connect(
  mapStateToProps,
  mapDispatchToProps
)(VideoRowApp);

export default VideoRow;