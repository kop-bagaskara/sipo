import './bootstrap';
import {DayPilot} from "daypilot-pro-javascript";

class App {
    scheduler = null;

    init() {
        this.scheduler = new DayPilot.Scheduler("scheduler", {
            treeEnabled: true,
            treePreventParentUsage: true,
            timeHeaders: [
                {groupBy: "Month"},
                {groupBy: "Day", format: "d"}
            ],
            scale: "Day",
            days: 365,
            startDate: "2025-01-01",
            rowHeaderColumns: [
                {name: "Name", display: "name"},
                {name: "Icon", width: 40},
                {name: "ID", display: "id"}
            ],
            cellWidth: 60,
            eventBorderRadius: 5,
            rowMarginBottom: 2,
            rowMarginTop: 2,
            contextMenu: new DayPilot.Menu({
                items: [
                    {
                        text: "Edit...",
                        onClick: args => {
                            this.editEvent(args.source);
                        }
                    },
                    {
                        text: "Delete",
                        icon: "delete",
                        onClick: async args => {
                            await axios.delete(`/api/events/${args.source.data.id}`);
                            this.scheduler.events.remove(args.source.data.id);
                        }
                    }
                ]
            }),
            onEventClick: args => {
                this.editEvent(args.e);
            },
            onBeforeEventRender: args => {
                args.data.backColor = "#93c47d";
                args.data.borderColor = "darker";
                args.data.fontColor = "white";

                args.data.barHidden = true;
                args.data.padding = "5px";

                args.data.areas = [
                    {
                        right: 3,
                        top: 10,
                        width: 16,
                        height: 16,
                        symbol: "/icons/daypilot.svg#x-2",
                        fontColor: "#555555",
                        onClick: async args => {
                            await axios.delete(`/api/events/${args.e.data.id}`);
                            this.scheduler.events.remove(args.e.data.id);
                        }
                    },
                    {
                        right: 23,
                        top: 10,
                        width: 16,
                        height: 16,
                        symbol: "/icons/daypilot.svg#threedots-v",
                        fontColor: "#444444",
                        action: "ContextMenu"
                    },
                ];
            },
            onBeforeCellRender: args => {
                if (args.cell.isParent) {
                    args.cell.backColor = "#f8f8f8";
                }
            },
            onBeforeRowHeaderRender: args => {

                const icon = args.row.data.icon;
                const column = args.row.columns[1];

                if (icon && column) {
                    column.areas = [
                        {
                            right: 0,
                            top: 0,
                            left: 0,
                            bottom: 0,
                            image: "/images/" + icon,
                            fontColor: "#555555",
                            style: "box-sizing: border-box; padding: 5px;",
                        }
                    ];
                }

            },
            onTimeRangeSelected: async args => {
                const modal = await DayPilot.Modal.prompt("Create a new event:", "Event 1");
                this.scheduler.clearSelection();
                if (modal.canceled) {
                    return;
                }
                const params = {
                    start: args.start,
                    end: args.end,
                    resource: args.resource,
                    text: modal.result
                };
                const {data} = await axios.post("/api/events", params);
                this.scheduler.events.add(data);
            },
            onEventMove: async args => {
                const params = {
                    id: args.e.data.id,
                    start: args.newStart,
                    end: args.newEnd,
                    resource: args.newResource,
                    text: args.e.data.text
                };
                await axios.put(`/api/events/${params.id}`, params);
            },
            onEventResize: async args => {
                const params = {
                    id: args.e.id(),
                    start: args.newStart,
                    end: args.newEnd,
                    resource: args.e.data.resource,
                    text: args.e.data.text
                };
                await axios.put(`/api/events/${params.id}`, params);
            },
        });
        this.scheduler.init();
        this.scheduler.scrollTo(DayPilot.Date.today());
        this.loadData();
    }

    resourcesFlat() {
        const resources = [];
        this.scheduler.resources.forEach(group => {
            group.children.forEach(resource => {
                resources.push(resource);
            });
        })
        return resources;
    }

    async editEvent(e) {
        const form = [
            {name: "Text", id: "text"},
            {name: "Start", id: "start", type: "datetime"},
            {name: "End", id: "end", type: "datetime"},
            {name: "Resource", id: "resource", options: this.resourcesFlat()}
        ];

        const modal = await DayPilot.Modal.form(form, e.data);
        if (modal.canceled) {
            return;
        }

        await axios.put(`/api/events/${e.data.id}`, modal.result);

        this.scheduler.events.update(modal.result);
    }

    async loadData() {
        const start = this.scheduler.visibleStart();
        const end = this.scheduler.visibleEnd();
        const [{data: resources}, {data: events}] = await Promise.all([
            axios.get("/api/resources"),
            axios.get(`/api/events?start=${start}&end=${end}`)
        ]);
        this.scheduler.update({resources, events});
    }
}

new App().init();
