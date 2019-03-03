import React, {Component} from 'react';
import {connect} from 'react-redux';

import YouTube from 'react-youtube';
import { Col, Panel, Button } from 'react-bootstrap';

import {setActiveYear} from "../reducers/activeYear";
import  FileList  from "./FileList";
import AddProblemModal from "./AddProblemModal";

class VideoReviewApp extends Component {
  constructor(props) {
    super(props);

    this.state = {
      loading: true,
      video: {},
      error: false,
      message: "",
      showProblemModal: false,
      problems: []
    };

    if(!this.props.activeYear) {
      props.setActiveYear(props.match.params.year);
    }

    this.fetchVideo(props.match.params.year, props.match.params.id)
  }

  fetchVideo(year, id) {
    console.log("Fetching Video: " + id);
    window.axios.get(`/api/video_review/${year}/fetch_video/${id}`)
      .then((response) => {
        console.log("Video Fetched");
        this.setState({ loading: false });
        return response.data;
      })
      .then((data) => {
        if(data.error) {
          this.setState({error: true, message: data.message});
          console.log("Video Response had and error, oh noes!\n" + data.message)
        } else {
          this.setState({ video: data });
        }
      })
      // .catch((err) => {
      //   console.log('Error Occurred while fetching video: ' + err)
      // });
  }

  showProblemModal(e) {
    e ? e.preventDefault() : null;
    this.setState({showProblemModal: true})
  }

  addProblemHandler(data) {
    this.setState({problems: this.state.problems.concat(data)})
  }

  hideProblemModal(e) {
    e ? e.preventDefault() : null;
    this.setState({showProblemModal: false})
  }

  render() {
    if(this.state.loading) {
      return <Col xs={12} key={"top_level_column"}>
        <h2 className="text-center">
          Loading . . .<br />
          <i className="fa fa-spinner fa-pulse fa-fw">{null}</i>
        </h2>
      </Col>
    } else if(this.state.error) {
      return <Col xs={12} key={"top_level_column"}>
        <h2 className="text-center">
          Error Loading Video
        </h2>
      </Col>
    } else {
      return [ <Col xs={12} key={"top_level_column"}>
        <AddProblemModal
          show={this.state.showProblemModal}
          hideHandler={(e) => this.hideProblemModal(e)}
          addProblemHandler={(data) => this.addProblemHandler(data)}
        />
        <Panel key={"video_viewer"}>
          <Panel.Heading key={"video_header"}>
            <h3 style={{marginTop: 5}} key={"video_title" + this.state.video.yt_code}>
              {this.state.video.name}
              <span className="pull-right">
                <Button onClick={(e) => this.showProblemModal(e)}>
                  <i className="fa fa-plus">{null}</i>&nbsp;
                  Add Problem
                </Button>
              </span>
            </h3>
          </Panel.Heading>
          <Panel.Body key={"video_body"}>
            <YouTube
              videoId={this.state.video.yt_code}
              opts={{ width: '100%', height: '100%' }}
              className="embed-responsive-item"
              containerClassName="embed-responsive embed-responsive-16by9"
              key={"video_continer_" + this.state.video.yt_code}
            />
          </Panel.Body>
        </Panel>
      </Col>,
      <Col xs={12} md={6} key={"filelist_col"}>
        <FileList files={this.state.video.files} />
      </Col> ]
    }
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
  return {
    activeYear: state.activeYear
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    setActiveYear: (year) => { dispatch(setActiveYear(year)) }
  }
}

const VideoReview = connect(
  mapStateToProps,
  mapDispatchToProps
)(VideoReviewApp);

export default VideoReview;