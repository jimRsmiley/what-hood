var CandidateNeighborhood = React.createClass({
  render: function() {
    var cn = this.props.candidate_neighborhood;
    var url = Whathood.UrlBuilder.neighborhood_by_name(cn.region.name, cn.name);
    return (
        <p><a href={url}>{cn.name}</a>: {cn.percentage}%</p>
    );
  }
});

var PointElection = React.createClass({
  render: function() {
    var point_election = this.props.point_election;
    var rows = new Array();
    for (var i = 0; i < point_election.candidate_neighborhoods.length; i++) {
      rows.push(<CandidateNeighborhood key={i} candidate_neighborhood={point_election.candidate_neighborhoods[i]} />);
    }
    return (
      <div className="point-election">
        {this.props.address}
        {rows}
        <p>
          <a href={this.props.browse_url}>Browse Neighborhoods at this location</a>
        </p>
      </div>
    );
  }
});
