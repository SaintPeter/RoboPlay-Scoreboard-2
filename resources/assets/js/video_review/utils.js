
export function formatTimestamp(time) {
  if(time) {
    const seconds = parseInt(time % 60);
    const display_seconds = seconds < 10 ? '0' + seconds : seconds;
    return parseInt(time / 60) + ":" + display_seconds;
  }
  return "0:00";
}