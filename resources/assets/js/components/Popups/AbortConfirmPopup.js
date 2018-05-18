import React, {Component} from "react";

export default class AbortConfirmPopup extends Component {
    componentDidMount() {
        this.$node = $(this.refs.popup);
        this.$node.popup({
            afterclose: () =>  {
                this.props.onCancel();
            },
            afteropen: () => {
                setTimeout(() => {
                    $.mobile.silentScroll(this.$node.parent().position().top);
                },1);
            }
        });
    }

    shouldComponentUpdate() {
        return false;
    }

    componentWillUnmount() {
        this.$node.popup("destroy");
    }

    componentWillReceiveProps(nextProps) {
        if(!this.props.visible && nextProps.visible) {
            this.$node.popup("open");
        }

        if(this.props.visible && !nextProps.visible) {
            this.$node.popup("close");
        }
    }

    render() {
        return (
            <div ref="popup" data-role="popup" data-history='false' id="abortPopup" className="ui-corner-all">
                <div role="banner" data-role="header" data-theme="a" className="ui-corner-top ui-header ui-bar-a">
                    <h1 aria-level="1" role="heading" className="ui-title">Confirm Abort?</h1>
                </div>
                <div role="main" className="ui-corner-bottom ui-content center">
                    <span className="bigtext">Run { this.props.runNumber }</span><br />
                    <span className="bigtext" style={{color: "red"}}>Abort</span><br />
                    <div
                       className="ui-link ui-btn ui-btn-a ui-btn-inline ui-shadow ui-corner-all"
                       onClick={this.props.onCancel}>Cancel</div>
                    <div className="ui-link ui-btn ui-btn-b ui-btn-inline ui-shadow ui-corner-all"
                       onClick={this.props.onAbort}>Confirm Abort</div>
                </div>
            </div>               
        )
    }
}

AbortConfirmPopup.defaultProps = {
    visible: false
};
