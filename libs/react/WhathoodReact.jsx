var CandidateNeighborhood = React.createClass({
  render: function() {
    var cn = this.props.candidate_neighborhood;
    return (
      <div>{cn.name}: {cn.percentage}%</div>
    );
  }
});

var PointElection = React.createClass({
  render: function() {
    var rows = new Array();
    for (var i = 0; i < this.props.candidate_neighborhoods.length; i++) {
      rows.push(<CandidateNeighborhood key={i} candidate_neighborhood={this.props.candidate_neighborhoods[i]} />);
    }
    return (
      <div className="point-election">
        {this.props.address}
        {rows}
      </div>
    );
  }
});
