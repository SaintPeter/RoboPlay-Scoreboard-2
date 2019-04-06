import React from 'react';

export function formatTimestamp(time) {
  if(time) {
    const seconds = parseInt(time % 60);
    const display_seconds = seconds < 10 ? '0' + seconds : seconds;
    return parseInt(time / 60) + ":" + display_seconds;
  }
  return "0:00";
}

export function lookupDetailType(problem) {
  let id = problem.video_review_details_id;
  return problemDetailList.hasOwnProperty(id) ? problemDetailList[id].reason : 'Unknown Problem';
}

export function formatTime(problem, changeTimeHandler) {
  if(problem.hasOwnProperty('timestamp') && problem.timestamp >= 0) {
    return (
      <a onClick={(e) => changeTimeHandler(e, problem.timestamp)}
         className="pull-right"
         title="Go to Timestamp"
         style={{cursor: "pointer"}}
      >
        {formatTimestamp(problem.timestamp)}
      </a>
    )
  } else {
    return null;
  }
}