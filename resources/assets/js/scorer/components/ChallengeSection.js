import React, { Component } from "react";
import {updateBackButton, updatePageTitle} from "../actions/Generic";
import {connect} from "react-redux";
const $ = window.$;
/*  Class to display scoreboard element for a single Challenge
*   for a single team
*
*   props:challengeData - array of challenge objects
*/

class ChallengeSectionApp extends Component {
  constructor(props) {
    super(props);
    this.state = {
      open: false,
      final: 0
    };
  }
  componentDidMount() {
    const _this = this;
    this.$node = $(this.refs.collapsible);
    this.$node.on("collapse", function(e) {
      _this.setState({
        open: false
      });
    });
    this.$node.on("expand", function(e) {
      _this.setState({
        open: true
      });
    });
    this.props.accordianObject[this.props.challengeId] = this.$node;
  }

  toggleAccordianState() {
    this.setState({ open: !this.state.open });
  }

  convertTimestamp(timestamp) {
    let ampm = "am";
    // Create a new JavaScript Date object based on the timestamp
    // multiplied by 1000 so that the argument is in milliseconds, not seconds.
    let date = new Date(timestamp * 1000);
    // Hours part from the timestamp
    let hours = date.getHours();
    if (hours > 12) {
      hours = hours - 12;
      ampm = "pm";
    }
    // Minutes part from the timestamp
    let minutes = "0" + date.getMinutes();

    return `(${hours}:${minutes.substr(-2)}${ampm})`;
  }

  totalScore(scores) {
    console.log("scores-", scores);
    return Object.keys(scores).reduce((acc, curr) => acc + scores[curr], 0);
  }

  getFinalScore(rows) {
    const ts = this.totalScore;
    return rows.reduce((max, row) => {
      const newScore = row.abort ? 0 : ts(row.scores);
      return Math.max(max, newScore);
    }, 0);
  }

  displayScores(scores) {
    return [0, 1, 2, 3, 4, 5].map((item, index, array) => {
      return (
        <td key={index}>
          {scores
            ? scores[index + 1] === undefined ? "-" : scores[index + 1]
            : "-"}
        </td>
      );
    });
  }

  displaySingleRun({ run, order, numRuns, finalScore }) {
    const total = run.scores ? this.totalScore(run.scores) : 0;

    return (
      <tr key={run.timestamp}>
        <td className="small">{`Run ${order} ${this.convertTimestamp(run.timestamp)}`}</td>
        {run.abort ? (
          <td colSpan="6" style={{ color: "#ccc", backgroundColor: "#f7f7f7" }}>
            Abort
          </td>
        ) : (
          this.displayScores(run.scores)
        )}
        <td>{total}</td>
        {order === 1 ? (
          <td rowSpan={numRuns} style={{ verticalAlign: "middle", textAlign: "center" }}>
            <h3>{finalScore}</h3>
          </td>
        ) : null}
      </tr>
    );
  }

  displayRuns({ runs, finalScore }) {
    //if no runs return
    if (!runs.length) {
      return (
        <tr>
          <td style={{ color: "#ccc", backgroundColor: "#f7f7f7" }} colSpan="8">
            No Record
          </td>
            <td style={{ verticalAlign: "middle", textAlign: "center" }}>
                <h3>0</h3>
            </td>
        </tr>
      );
    }
    //otherwise figure final score & create row for each run
    return runs.map((run, index, array) => {
      return this.displaySingleRun({
        run,
        order: index + 1,
        numRuns: array.length + 1,
        finalScore
      });
    });
  }

  render() {
    const runs = this.props.scores.filter(run => {
      return run.chalId === this.props.challengeData.id;
    });
    const finalScore = this.getFinalScore(runs);

    return (
      <div ref="collapsible" data-role="collapsible" id={"expandme" + this.props.challengeData.id}>
        <h4>
            {this.props.challengeData.display_name}
          <span className="pull-right">
            {finalScore}/{this.props.challengeData.points}
          </span>
        </h4>
        <div
          id={`table${this.props.challengeId}`}
          style={{
              padding:0
          }}
        >
          <table
            className="table table-bordered score-table"
            style={{ marginBottom: "0" }}
            id="demo"
          >
            <tbody>
              <tr>
                <th>Score Elements </th>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>Total</th>
                <th>Score</th>
              </tr>
              {this.displayRuns({ runs, finalScore })}
            </tbody>
          </table>
        </div>
      </div>
    );
  }
}

// Map Redux state to component props
function mapStateToProps(state) {
    return {
        //challengeData: state.challengeData,
    }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
    return {
        updateBack: (newURL, show) => dispatch(updateBackButton(newURL, show)),
        updateTitle: (newTitle) => dispatch(updatePageTitle(newTitle))
    }
}

const ChallengeSection =  connect(
    mapStateToProps,
    mapDispatchToProps
)(ChallengeSectionApp);

export default ChallengeSection;
