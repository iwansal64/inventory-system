const graphs = Array.from(document.getElementsByClassName("graph"));
let graph_datas = [];

/// Storing Graph Data
graphs.forEach(graph => {
    graph_datas.push([]);
    const graph_childrens = Array.from(graph.children);
    graph_childrens.entries().forEach(([index, graph_data_set]) => {
        graph_datas[graph_datas.length - 1].push({});
        graph_datas[graph_datas.length - 1][index]["key"] = graph_data_set.children[0].getAttribute("data-key");
        graph_datas[graph_datas.length - 1][index]["value"] = Number.parseInt(graph_data_set.children[1].getAttribute("data-value"));
    });
});


/// GRAPHS BUILDER
graphs.entries().forEach(([graph_index, graph]) => {
    const current_graph_data = graph_datas[graph_index];
    const graph_height = Number.parseInt(window.getComputedStyle(graph).height.split("p", 2)[0]);
    const max_value = Math.max(...(current_graph_data.map((data) => Number.parseInt(data["value"])))) + 1;
    const gap_y = graph_height / max_value;

    /// Creating X Scroll Container
    const x_scroll_wrapper = document.createElement("div");
    x_scroll_wrapper.classList.add("x-scroll-wrapper");
    graph.appendChild(x_scroll_wrapper);

    const x_scroll_container = document.createElement("div");
    x_scroll_container.classList.add("x-scroll-container");
    x_scroll_container.style.minWidth = graph.getAttribute("data-min-x-size");
    x_scroll_wrapper.appendChild(x_scroll_container);

    const x_scroll_container_width = Number.parseInt(window.getComputedStyle(x_scroll_container).width.split("p", 2)[0]);
    const gap_x = x_scroll_container_width / current_graph_data.length;

    /// Creating Grid Lines
    //? y-axis
    for (let i = 0; i <= current_graph_data.length; i++) {
        const pos_x = i * gap_x;

        const grid_line_y = document.createElement("div");
        grid_line_y.classList.add("grid");
        grid_line_y.classList.add("grid-y");
        grid_line_y.style.left = pos_x + "px";

        x_scroll_container.appendChild(grid_line_y);
    }

    //? x-axis
    for (let i = 0; i < max_value; i++) {
        const pos_y = i * gap_y;

        const grid_line_x = document.createElement("div");
        grid_line_x.classList.add("grid");
        grid_line_x.classList.add("grid-x");
        grid_line_x.style.top = pos_y + "px";

        graph.appendChild(grid_line_x);
    }

    /// Axis Numbering
    //? x-axis
    for (let i = 1; i <= current_graph_data.length; i++) {
        const pos_x = i * gap_x;

        const axis_number = document.createElement("p");
        axis_number.classList.add("axis-number");
        axis_number.classList.add("x-axis");
        axis_number.style.left = pos_x + "px";
        axis_number.style.top = (max_value * gap_y) + "px";
        axis_number.innerText = current_graph_data[i - 1]["key"];

        x_scroll_container.appendChild(axis_number);
    }

    //? y-axis
    for (let i = 1; i <= max_value; i++) {
        const pos_y = (max_value - i) * gap_y;
        const axis_number = document.createElement("p");
        axis_number.classList.add("axis-number");
        axis_number.classList.add("y-axis");
        axis_number.style.top = pos_y + "px";
        axis_number.innerText = i;

        graph.appendChild(axis_number);
    }

    //? Origin
    const origin_number = document.createElement("p");
    origin_number.classList.add("axis-number");
    origin_number.classList.add("origin");
    origin_number.style.top = (max_value * gap_y) + "px";
    origin_number.innerText = "0";
    graph.appendChild(origin_number);


    /// Place Points Into Graph
    current_graph_data.entries().forEach(([index, { key, value }]) => {
        const point = document.createElement("div");
        point.classList.add("point");
        point.style.left = ((index + 1) * gap_x) + "px";
        point.style.top = ((max_value - value) * gap_y) + "px";
        if (value >= max_value - 1) {
            point.title = `At ${key}, ${value} borrow data has been registered. It's the highest demand till this day`;
        }
        else {
            point.title = `At ${key}, ${value} borrow data has been registered.`;
        }

        x_scroll_container.appendChild(point);
    });

    /// Place Lines Into Graph
    for (let index = 1; index < current_graph_data.length; index++) {
        const y_value_1 = current_graph_data[index - 1]["value"];
        const y_value_2 = current_graph_data[index]["value"];
        const delta_x = 1;
        const delta_y = y_value_2 - y_value_1;

        const width = Math.sqrt(Math.pow(delta_y * gap_y, 2) + Math.pow(delta_x * gap_x, 2));
        const degree = -Math.atan((delta_y * gap_y) / (delta_x * gap_x));
        const pos_x = gap_x * ((index + 1) - 0.5) - (width / 2);
        const pos_y = gap_y * (max_value - ((y_value_1 + y_value_2) / 2));


        const line = document.createElement("div");
        line.classList.add("line");
        line.style.width = width + "px";
        line.style.left = pos_x + "px";
        line.style.top = pos_y + "px";
        line.style.transform = "rotate(" + degree + "rad)";

        x_scroll_container.appendChild(line);
    }
});
