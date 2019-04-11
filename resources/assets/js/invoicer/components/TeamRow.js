import React, {Component} from 'react';
import {connect} from 'react-redux';

import {updateTeamDivision, updateTeamChecked} from '../reducers/invoiceData'

class TeamRowApp extends Component {

  teamDivisonChangeHandler = (e, teamId) => {
    console.log(`Update Division - Invoice: ${this.props.invoiceId} Team: ${teamId} changed to ${e.target.value}`);
    this.props.updateTeamDivision(this.props.invoiceId,teamId,e.target.value);
  };

  teamCheckedClickHandler = (teamId) => {
    console.log(`Update Checked - Invoice: ${this.props.invoiceId} Team: ${teamId} Toggle`);
    this.props.updateTeamChecked(this.props.invoiceId,teamId);
  };

  render() {
    let teamData = this.props.rowData;
    let divData = this.props.divData;

    if (teamData && teamData.length > 0 && (this.props.showAllTeams || this.props.showTeamsList.hasOwnProperty(this.props.invoiceId))) {
      return <tr key={"teams_" + this.props.invoiceId}>
        <td colSpan="7" className="team_section" id="teams{{ $invoice->id }}">
          <table className="table pull-right">
            <thead>
            <tr>
              <th>Team Name</th>
              <th>Division</th>
              <th className="text-center">Students</th>
              <th>Status</th>
            </tr>
            </thead>
            <tbody>
            {teamData.map(team => {
              return [<tr  key={"teams_" + this.props.invoiceId + "_meta"}>
                <td>
                  {team.name}
                </td>
                <td>
                  <TeamDropDown
                    divData={divData}
                    onChange={() => this.teamDivisonChangeHandler(team.id)}
                    value={team.division_id}
                  />
                </td>
                <td className="text-center">{team.student_count}</td>
                <td>
                  <CheckedButton
                    onClick={() => {this.teamCheckedClickHandler(team.id)}}
                    status={team.status}
                    />
                </td>
              </tr>,
                team.student_count ?
                  <tr  key={"teams_" + this.props.invoiceId + "_students"}>
                    <td>{null}</td>
                    <td>
                      <table className="table table-condensed">
                        <thead>
                        <tr>
                          <th>Student</th>
                          <th>Math Level</th>
                          <th className="text-center">Math Div</th>
                          <th className="text-center">Grade</th>
                          <th className="text-center">T-Shirt</th>
                        </tr>
                        </thead>
                        <tbody>
                        {team.students.map(student => {
                          return <tr key={student.id}>
                            <td>{student.name}</td>
                            <td>{student.math_level_name}</td>
                            <td className="text-center">{student.math_level}</td>
                            <td className="text-center">{student.grade}</td>
                            <td className="text-center">{student.tshirt}</td>
                          </tr>
                        })
                        }
                        </tbody>
                      </table>
                    </td>
                    <td colSpan={2}>&nbsp;</td>
                  </tr>
                  :
                  null
              ]
            })}
            </tbody>
          </table>
        </td>
      </tr>
    } else {
      return null;
    }
  }
}

class TeamDropDown extends Component {
  render() {
    return <select onChange={this.props.onChange} value={this.props.value}>
      {
        Object.entries(this.props.divData).map(category => {
          return <optgroup key={category[0]} label={category[0]}>
            {
              Object.entries(category[1]).map(items => {
                return <option key={items[0]} value={items[0]}>{items[1]}</option>
              })
            }
          </optgroup>
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
    showTeamsList: state.showTeamsList,
    showAllTeams: state.showAllTeams
  }
}

// Map Redux actions to component props
function mapDispatchToProps(dispatch) {
  return {
    updateTeamDivision: (invoiceId,teamId,newDivsion) => dispatch(updateTeamDivision(invoiceId,teamId,newDivsion)),
    updateTeamChecked: (invoiceId,teamId) => dispatch(updateTeamChecked(invoiceId,teamId))
  }
}

const TeamRow = connect(
  mapStateToProps,
  mapDispatchToProps
)(TeamRowApp);

export default TeamRow;