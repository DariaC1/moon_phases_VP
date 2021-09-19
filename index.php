<!DOCTYPE html>

<head>
  <meta charset="utf-8">
  <title>Moon Phases</title>
  <style>
    body {
      margin: 0;
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background-color: #DCDCDC;
      
    }
    text {
      font-size: 17px;
      fill: black;
      font-family: "Times New Roman", Times, serif;
    }
    .axis .domain,
    .axis line {
      display: none;
    }
    button{
      margin: 10px;
      border-radius: 4px;
      background-color: #f7f6f2;
      color: #2b281b;
      border: 2px solid #2b281b;
      padding: 6px 12px;
    }
    button:hover{
      background-color: #DCDCDC;
    }
    button:active{
     background-color: gray; 
    }
    input{
      border-radius: 4px;
      padding: 6px 12px;
      background-color: #f7f6f2;
    }
    .buttons{
      margin-left: 120px;
    }
    .button-animate{
      margin-left: 615px;
    }
  </style>
</head>

<body>
  <script src="https://d3js.org/d3.v4.min.js"></script>
  <svg width="1460" height="630"></svg>
  <div class = "buttons">
    <button onclick="onCurrent()">Current day</button>
    <button onclick="yearmoons()">Change year</button>
    <input type = "number" id = "years" placeholder = "year">
    <button class="button-animate"  onclick="animationSetup()">Animate</button>
  </div>
  <script>
  
    var svg = d3.select("svg");
      //.attr("width", document.body.clientWidth)
      //.attr("height", document.body.clientHeight - 100)

    var width = svg.attr("width"),
        height = svg.attr("height"),
        margin = { left: 140, right: 120, top: 100, bottom: 60 },
        moonSize = 12,
        moonBig = 100,
        moonS = 40,
        moonStroke = 0.5,
        axisPadding = moonSize - 2;
      
    var year = 2021,
        startDate = new Date(year, 0, 1),
        endDate = new Date(year, 11, 32);

    var g = svg.append("g")
      .attr("transform", "translate(" + [margin.left, margin.top] + ")"),
      innerWidth = width - margin.right - margin.left - 380,
      innerHeight = height - margin.bottom - margin.top,
      formatMonth = d3.timeFormat("%B"),
      formatDay = d3.timeFormat("%d"),
      newMoon = (new Date(1970, 0, 7, 20, 35, 0)).getTime(),
      lunarPeriod = 2551443000,
      data = d3.timeDays(startDate, endDate).map(type);

    let bigmoon, redcircle;

    function type(date) {
      return {
        month: formatMonth(date),
        day: formatDay(date),
        phase: -((date.getTime() - newMoon)) / lunarPeriod * 360 + 180
      };
    }

    var xScale = d3.scaleLinear()
      .domain(d3.extent(data, function (d) { return d.day; }))
      .range([0, innerWidth]);
    var yScale = d3.scalePoint()
        .domain(data.map(function (d) { return d.month; }))
        .range([0, innerHeight]);

    // za crtanje path za male mjesece - mora biti različiti zbog projection veličine koja je ranije definirana
    var circle = d3.geoCircle();

    var projection = d3.geoOrthographic().scale(moonSize).translate([0, 0]),
      path = d3.geoPath().projection(projection);

    // za crtanje path za veliki mjesec
    let projectionBig = d3.geoOrthographic().scale(moonBig).translate([0, 0]),
      pathBig = d3.geoPath().projection(projectionBig);

    let lil_projection = d3.geoOrthographic().scale(moonS).translate([0, 0]),
      lil_path = d3.geoPath().projection(lil_projection);


    g.append("g")   // to je za ispisivanje mjeseci sa strane
      .attr("class", "y axis")
      .attr("transform", "translate(-" + axisPadding + ")")
      .call(d3.axisLeft().scale(yScale));

    g.append("g")  // to je za ispis dana gore
      .attr("class", "x axis")
      .attr("transform", "translate(0,-" + axisPadding + ")")
      .call(d3.axisTop().scale(xScale).ticks(30));

    g.append("g") // to je za ispis dana dolje
      .attr("class", "x axis")
      .attr("transform", "translate(0," + (innerHeight + axisPadding) + ")")
      .call(d3.axisBottom().scale(xScale).ticks(30));

    var moons = g.selectAll(".moon").data(data)
      .enter().append("g")
      .attr("class", "moon")
      .attr("transform", function (d) { return "translate(" + [xScale(d.day), yScale(d.month)] + ")"; });

    moons.append("circle")
      .attr("fill", "#2b281b")  //tamna boja, crta crne krugove
      .attr("r", moonSize + moonStroke)
      .on('click', function (d) { drawBigMoon(d) })

    var year_text = svg.append("text").attr("x", 1170).attr("y", 100).text(function(){ return ("Year: " + year);});
    
    var date_text;

    var prev_year;

    function yearmoons(){
          prev_year = year;
          year = document.getElementById("years").value;
          if(prev_year == year){return 0;}
          if(year < 0){
            if(moons){moons.remove();}
            if(date_text){date_text.remove();}
            if(bigmoon){bigmoon.remove();}
            if(redcircle){redcircle.remove();}
          }

          if(year > 0){
            if(moons){moons.remove();}
            if(date_text){date_text.remove();}
            if(bigmoon){bigmoon.remove();}
            if(redcircle){redcircle.remove();}

            startDate = new Date(year, 0, 1);
            endDate = new Date(year, 11, 32);
            data = d3.timeDays(startDate, endDate).map(type);

            function type(date) {
                return {
                  month: formatMonth(date),
                  day: formatDay(date),
                  phase: -((date.getTime() - newMoon)) / lunarPeriod * 360 + 180
                };
            
        }
          year_text.text(function(){ return ("Year: " + year);});

          moons = g.selectAll(".moon").data(data)
            .enter().append("g")
            .attr("class", "moon")
            .attr("transform", function (d) { return "translate(" + [xScale(d.day), yScale(d.month)] + ")";});

          moons.append("circle")
            .attr("fill", "#2b281b")  
            .attr("r", moonSize + moonStroke)
            .on('click', function (d) {drawBigMoon(d) })

          moons.append("path")
            .attr("fill", "#f7f6f2")
            .on('click', function (d) {drawBigMoon(d)})
            .attr("d", function (d) { return path(circle.center([d.phase, 0])());})}
    }

    var enter = document.getElementById("years");
    enter.addEventListener("keydown", function (e) { if (e.code === "Enter") { yearmoons();} });

    function drawBigMoon(element) {

      if (bigmoon) { bigmoon.remove(); 
                     redcircle.remove(); }

      if (date_text) { date_text.remove(); }

      bigmoon = svg.append('g').attr("transform", "translate(1200, 240)")
      bigmoon.append("circle").attr("fill", "#2b281b").attr("r", moonBig).attr("stroke", "#2b281b").attr('stroke-width', 1)
      bigmoon.append("path")
        .attr("fill", "#f7f6f2")
        .attr("d", function () { return pathBig(circle.center([element.phase, 0])());});

      redcircle = g.append('g') // ovdje g da se ne mora pomicati zbog margina
      redcircle.append("circle")
              .attr("cx", function (){ return (xScale(element.day)); }) 
              .attr("cy", function (){ return (yScale(element.month)); }) 
              .attr("stroke", "red")
              .attr("stroke-width", "50px")
              .attr("stroke-opacity", 0)
              .attr("fill", "none")
              .attr("r", 500)
            .transition().duration(300)
              .attr("stroke-width", "3px")
              .attr("stroke-opacity", 1)
              .attr("r", moonSize + 5);
        
        date_text = svg.append("text").attr("x", 1170).attr("y", 380).text("Date");
        date_text.text(function(){ return (element.day + ' ' + element.month);});
    }

    // pozivanje funkcije za crtanje velikog mjeseca na klik i crtanje path malih mjeseca
    moons.append("path")
      .attr("fill", "#f7f6f2")
      .on('click', function (d) {drawBigMoon(d) })
      .attr("d", function (d) { return path(circle.center([d.phase, 0])());})
    
    var tday = type(new Date());

    function onCurrent(){ if(year == 2021) {drawBigMoon(tday)}; }

    var lil_moon = svg.append('g').attr("transform", "translate(1200, 500)")
        lil_moon.append("circle").attr("fill", "#2b281b").attr("r", moonS).attr("stroke", "#2b281b").attr('stroke-width', 2);

    var intervalFunction, animationPhase, moonPhase, little_moon_text;

    moonPhase = lil_moon.append("path")
        .attr("fill", "#2b281b")
        .attr("d", function () { return lil_path(circle.center([animationPhase, 0])()); });

    little_moon_text = lil_moon.append("text")
        .style("font-size", "15px")
        .attr("x", -moonS * 1.7)
        .attr("y", moonS * 2)
    little_moon_text.text("Moon phase: New moon"); 


    function animationSetup() {

        // mladi mjesec -229141.398126472 

        // puni mjesec -227154.8140640414

        animationPhase = 360

        //kon vrj = -228967.398126472

        if (lil_moon.select("circle").nodes().length > 0) {
          lil_moon.remove();
          lil_moon = svg.append('g').attr("transform", "translate(1200, 500)")
          lil_moon.append("circle").attr("fill", "#2b281b").attr("r", moonS).attr("stroke", "#2b281b").attr('stroke-width', 3);
        }

        lil_moon.append("circle")
          .attr("fill", "#f7f6f2")
          .attr("r", moonS);

        moonPhase = lil_moon.append("path")
          .attr("fill", "#2b281b") 
          .attr("d", function () { return lil_path(circle.center([animationPhase, 0])()); });

        little_moon_text = lil_moon.append("text")
          .style("font-size", "15px")
          .attr("x", -moonS * 1.7)
          .attr("y", moonS * 2)
        little_moon_text.text("Moon phase: New moon"); 

        intervalFunction = setInterval(animation, 10)
    }

    function animation() {

        animationPhase -= 0.1;
        moonPhase.remove();

        moonPhase = lil_moon.append("path")
          .attr("fill", "#2b281b")
          .attr("d", function () { return lil_path(circle.center([animationPhase, 0])()); });
    
        if (animationPhase >= 345 && animationPhase <= 360) {
              little_moon_text.text("Moon phase: New moon");
        } else if (animationPhase >= 310 && animationPhase <= 320) {
              little_moon_text.text("Moon phase: Waning Crescent");
        } else if (animationPhase >= 265 && animationPhase <= 275) {
              little_moon_text.text("Moon phase: First Quarter");
        } else if (animationPhase >= 220 && animationPhase <= 230) {
              little_moon_text.text("Moon phase: Waning Gibbous");
        } else if (animationPhase >= 175 && animationPhase <= 185) {
              little_moon_text.text("Moon phase: Full moon");
        } else if (animationPhase >= 130 && animationPhase <= 140) {
              little_moon_text.text("Moon phase: Waxing Gibbous");
        } else if (animationPhase >= 85 && animationPhase <= 95) {
              little_moon_text.text("Moon phase: Third quarter");
        } else if (animationPhase >= 40 && animationPhase <= 50) {
              little_moon_text.text("Moon phase: Waxing crescent");
        } else if (animationPhase >= 0 && animationPhase <= 10) {
              little_moon_text.text("Moon phase: New moon");
        } else {
              little_moon_text.text("");
        } 

        if (animationPhase <= 0) { 
          little_moon_text.text("Moon phase: New moon")
          clearInterval(intervalFunction);}
} 
  </script>

</body>

</html>