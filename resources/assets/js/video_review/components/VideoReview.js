import React, {Component} from 'react';
import {connect} from 'react-redux';

import YouTube from 'react-youtube';
import { Row, Col, Panel, Button } from 'react-bootstrap';

import {setActiveYear} from "../reducers/activeYear";
import FileList  from "./FileList";
import AddProblemModal from "./AddProblemModal";
import ProblemList from "./ProblemList";
import PriorProblemList from "./PriorProblemList";

class VideoReviewApp extends Component {
  constructor(props) {
    super(props);

    this.state = {
      loading: true,
      video: {},
      error: false,
      message: "",
      showProblemModal: false,
      problems: [],
      timestamp: 0,
    };

    this.timePoll = null;
    this.playerReference = null;

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

  addProblemHandler(data, saveTimestamp) {
    // Default values
    data['timestamp'] = saveTimestamp ? this.state.timestamp : -1;
    data['comment'] = data.hasOwnProperty('comment') ? data['comment'] : '';

    this.setState({problems: this.state.problems.concat(data)})
  }

  changeTimeHandler(e, time, thisRef) {
    e ? e.preventDefault() : null;
    if(thisRef.playerReference) {
      thisRef.playerReference.seekTo(time)
    }
  }

  hideProblemModal(e) {
    e ? e.preventDefault() : null;
    this.setState({showProblemModal: false})
  }

  tickHandler(e) {
    const seconds = e.target.getCurrentTime();
    if(seconds != this.state.timestamp) {
      this.setState({timestamp: seconds});
    }
  }

  readyHandler(e) {
    this.playerReference = e.target;
    if(!this.timePoll) {
      this.timePoll = setInterval(() => {
        this.tickHandler(e);
      }, 1500)
    }
  }

  componentWillUnmount() {
    clearInterval(this.timePoll);
  }

  cancelHandler() {
    console.log("Canceled Review");
    this.props.history.push(`/${this.props.activeYear}/`)
  }

  deleteHandler(index) {
    this.setState({
      problems: this.state.problems.filter((_,i) => i !== index)
    })
  }

  saveProblemsHandler() {
    if(this.state.problems.length) {
      console.log("Save Problems");
      window.axios.post(`/api/video_review/save_problems/${this.props.match.params.id}`,{ problems: this.state.problems })
        .then((result) => {
          console.log("Problems for video saved");
          this.props.history.push(`/${this.props.activeYear}/`)
        })
    } else {
      console.log("Save No Problems");
      window.axios.get(`/api/video_review/save_no_problems/${this.props.match.params.id}`)
        .then((result) => {
          console.log("No Problems for video saved");
          this.props.history.push(`/${this.props.activeYear}/`)
        })
    }
  }

  render() {
    if(this.state.loading) {
      return <Row>
        <Col xs={12} key={"top_level_column"}>
          <h2 className="text-center">
            Loading . . .<br />
            <i className="fa fa-spinner fa-pulse fa-fw">{null}</i>
          </h2>
        </Col>
      </Row>
    } else if(this.state.error) {
      return <Row>
        <Col xs={12} key={"top_level_column"}>
          <h2 className="text-center">
            Error Loading Video
          </h2>
        </Col>
      </Row>
    } else {
      return <Row>
        <Col xs={12} key={"top_level_column"}>
          <AddProblemModal
            show={this.state.showProblemModal}
            hideHandler={(e) => this.hideProblemModal(e)}
            addProblemHandler={(data, saveTimestamp) => this.addProblemHandler(data, saveTimestamp)}
            timestamp={this.state.timestamp}
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
                opts={{width: '100%', height: '100%'}}
                className="embed-responsive-item"
                containerClassName="embed-responsive embed-responsive-16by9"
                key={"video_continer_" + this.state.video.yt_code}
                onReady={e => this.readyHandler(e)}
              />
            </Panel.Body>
          </Panel>
        </Col>
        <Col xs={12} md={6} key={"filelist_col"}>
          <FileList files={this.state.video.files}/>
        </Col>
        <Col xs={12} md={6} key={"problemlist_col"}>
          <PriorProblemList
            problems={this.state.video.problems}
            changeTimeHandler={(e, time) => this.changeTimeHandler(e, time, this)}
          />
          <ProblemList
            problems={this.state.problems}
            changeTimeHandler={(e, time) => this.changeTimeHandler(e, time, this)}
            cancelHandler={() => this.cancelHandler()}
            saveHandler={() => this.saveProblemsHandler()}
            deleteHandler={(i) => this.deleteHandler(i)}
          />
        </Col>
      </Row>
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