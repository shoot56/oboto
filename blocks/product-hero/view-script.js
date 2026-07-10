(function () {
    var SELECTOR = '[data-product-hero-diagram]';
    var LOGICAL_WIDTH = 680;
    var LOGICAL_HEIGHT = 540;
    var FOV = 820;

    var NODES = [
        { label: 'Claude', x: -255, y: -170, z: 0.4, cat: 'client' },
        { label: 'Cursor', x: -285, y: -92, z: -0.2, cat: 'client' },
        { label: 'ChatGPT', x: -270, y: -18, z: 0.2, cat: 'client' },
        { label: 'Agent', x: -292, y: 58, z: 0.1, cat: 'client' },
        { label: 'VS Code', x: -260, y: 132, z: 0.3, cat: 'client' },
        { label: 'Workflow', x: -300, y: 210, z: -0.1, cat: 'client' },
        { label: 'Filesystem', x: -78, y: -152, z: 0.2, cat: 'local' },
        { label: 'Git', x: 82, y: -130, z: -0.2, cat: 'local' },
        { label: 'Shell', x: -94, y: 146, z: 0.1, cat: 'local' },
        { label: 'Docker', x: 90, y: 164, z: 0.35, cat: 'local' },
        { label: 'Slack', x: 258, y: -178, z: 0.3, cat: 'remote' },
        { label: 'GitHub', x: 308, y: -100, z: -0.1, cat: 'remote' },
        { label: 'Notion', x: 292, y: -18, z: 0.45, cat: 'remote' },
        { label: 'Outlook', x: 306, y: 62, z: 0.1, cat: 'remote' },
        { label: 'Postgres', x: 270, y: 140, z: 0.25, cat: 'remote' },
        { label: 'Salesforce', x: 314, y: 218, z: -0.25, cat: 'remote' }
    ];

    var COLORS = {
        client: { r: 91, g: 155, b: 255, hex: '#5b9bff', text: '#a9c4ff' },
        local: { r: 16, g: 185, b: 129, hex: '#10b981', text: '#8df2c8' },
        remote: { r: 143, g: 143, b: 247, hex: '#8f8ff7', text: '#c9c9ff' }
    };

    function rgba(color, alpha) {
        return 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' + alpha + ')';
    }

    function project(x, y, z) {
        var scale = FOV / (FOV + z * 90);
        return {
            x: LOGICAL_WIDTH / 2 + x * scale,
            y: LOGICAL_HEIGHT / 2 + y * scale,
            scale: scale
        };
    }

    function bezierPoint(t, p0, p1, p2, p3) {
        var u = 1 - t;

        return {
            x: u * u * u * p0.x + 3 * u * u * t * p1.x + 3 * u * t * t * p2.x + t * t * t * p3.x,
            y: u * u * u * p0.y + 3 * u * u * t * p1.y + 3 * u * t * t * p2.y + t * t * t * p3.y
        };
    }

    function hexPoints(cx, cy, radius, rotation) {
        var points = [];

        for (var index = 0; index < 6; index += 1) {
            var angle = Math.PI / 3 * index + rotation;
            points.push({
                x: cx + radius * Math.cos(angle),
                y: cy + radius * Math.sin(angle)
            });
        }

        return points;
    }

    function drawHex(ctx, cx, cy, radius, rotation) {
        var points = hexPoints(cx, cy, radius, rotation);

        ctx.beginPath();
        points.forEach(function (point, index) {
            if (index === 0) {
                ctx.moveTo(point.x, point.y);
            } else {
                ctx.lineTo(point.x, point.y);
            }
        });
        ctx.closePath();
    }

    function initDiagram(canvas) {
        if (!canvas || canvas.dataset.productHeroInitialized === 'true') {
            return;
        }

        var ctx = canvas.getContext('2d');
        if (!ctx) {
            return;
        }

        canvas.dataset.productHeroInitialized = 'true';

        var reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        var frameId = null;
        var dpr = 1;
        var cssWidth = 0;
        var cssHeight = 0;
        var particles = NODES.map(function (node, index) {
            return {
                index: index,
                progress: (index * 0.071) % 1,
                speed: 0.08 + (index % 5) * 0.015,
                outbound: node.cat !== 'remote'
            };
        });

        function resize() {
            var rect = canvas.getBoundingClientRect();
            cssWidth = Math.max(1, Math.round(rect.width));
            cssHeight = Math.max(1, Math.round(rect.height));
            dpr = Math.min(window.devicePixelRatio || 1, 2);

            canvas.width = Math.round(cssWidth * dpr);
            canvas.height = Math.round(cssHeight * dpr);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            draw(performance.now());
        }

        function getScreenNodes(time) {
            var center = { x: LOGICAL_WIDTH / 2, y: LOGICAL_HEIGHT / 2 };

            return NODES.map(function (node, index) {
                var driftX = reduceMotionQuery.matches ? 0 : Math.sin(time * 0.00045 + index * 1.8) * 7;
                var driftY = reduceMotionQuery.matches ? 0 : Math.cos(time * 0.00055 + index * 1.2) * 6;
                var position = project(node.x + driftX, node.y + driftY, node.z);
                var controlA = {
                    x: position.x + (center.x - position.x) * 0.38 + Math.sin(index * 1.7) * 36,
                    y: position.y + (center.y - position.y) * 0.38 + Math.cos(index * 1.2) * 32
                };
                var controlB = {
                    x: center.x + Math.cos(index * 1.4) * 42,
                    y: center.y + Math.sin(index * 1.1) * 34
                };

                return {
                    node: node,
                    position: position,
                    controlA: controlA,
                    controlB: controlB
                };
            });
        }

        function draw(time) {
            var scale = Math.min(cssWidth / LOGICAL_WIDTH, cssHeight / LOGICAL_HEIGHT);
            var offsetX = (cssWidth - LOGICAL_WIDTH * scale) / 2;
            var offsetY = (cssHeight - LOGICAL_HEIGHT * scale) / 2;
            var center = { x: LOGICAL_WIDTH / 2, y: LOGICAL_HEIGHT / 2 };
            var screenNodes = getScreenNodes(time);

            ctx.clearRect(0, 0, cssWidth, cssHeight);
            ctx.save();
            ctx.translate(offsetX, offsetY);
            ctx.scale(scale, scale);

            var background = ctx.createRadialGradient(center.x, center.y, 0, center.x, center.y, 260);
            background.addColorStop(0, 'rgba(59, 130, 246, 0.18)');
            background.addColorStop(1, 'rgba(59, 130, 246, 0)');
            ctx.fillStyle = background;
            ctx.fillRect(0, 0, LOGICAL_WIDTH, LOGICAL_HEIGHT);

            screenNodes.forEach(function (item) {
                var color = COLORS[item.node.cat];
                ctx.setLineDash([3, 9]);
                ctx.strokeStyle = rgba(color, 0.12);
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(item.position.x, item.position.y);
                ctx.bezierCurveTo(item.controlA.x, item.controlA.y, item.controlB.x, item.controlB.y, center.x, center.y);
                ctx.stroke();
            });
            ctx.setLineDash([]);

            if (!reduceMotionQuery.matches) {
                particles.forEach(function (particle) {
                    var item = screenNodes[particle.index];
                    var color = COLORS[item.node.cat];
                    var progress = (particle.progress + time * 0.001 * particle.speed) % 1;
                    var t = particle.outbound ? progress : 1 - progress;
                    var point = bezierPoint(
                        t,
                        item.position,
                        item.controlA,
                        item.controlB,
                        center
                    );
                    var glow = ctx.createRadialGradient(point.x, point.y, 0, point.x, point.y, 12);
                    glow.addColorStop(0, rgba(color, 0.72));
                    glow.addColorStop(1, rgba(color, 0));
                    ctx.fillStyle = glow;
                    ctx.beginPath();
                    ctx.arc(point.x, point.y, 12, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.fillStyle = color.hex;
                    ctx.beginPath();
                    ctx.arc(point.x, point.y, 2.4, 0, Math.PI * 2);
                    ctx.fill();
                });
            }

            screenNodes
                .slice()
                .sort(function (a, b) {
                    return a.node.z - b.node.z;
                })
                .forEach(function (item) {
                    var color = COLORS[item.node.cat];
                    var radius = Math.max(18, (24 + item.node.z * 4) * item.position.scale);
                    var nodeGlow = ctx.createRadialGradient(item.position.x, item.position.y, 0, item.position.x, item.position.y, radius * 2.4);

                    nodeGlow.addColorStop(0, rgba(color, 0.15));
                    nodeGlow.addColorStop(1, rgba(color, 0));
                    ctx.fillStyle = nodeGlow;
                    ctx.beginPath();
                    ctx.arc(item.position.x, item.position.y, radius * 2.4, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.beginPath();
                    ctx.arc(item.position.x, item.position.y, radius, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(8, 14, 32, 0.92)';
                    ctx.fill();
                    ctx.strokeStyle = rgba(color, 0.44);
                    ctx.lineWidth = 1.2;
                    ctx.stroke();

                    ctx.fillStyle = color.text;
                    ctx.font = '500 10px monospace';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(item.node.label, item.position.x, item.position.y);
                });

            var pulse = reduceMotionQuery.matches ? 0 : Math.sin(time * 0.002) * 0.12;
            var outerRadius = 82 + pulse * 16;
            var outerGlow = ctx.createRadialGradient(center.x, center.y, 0, center.x, center.y, outerRadius * 1.7);
            outerGlow.addColorStop(0, 'rgba(59, 130, 246, 0.38)');
            outerGlow.addColorStop(1, 'rgba(59, 130, 246, 0)');
            ctx.fillStyle = outerGlow;
            ctx.beginPath();
            ctx.arc(center.x, center.y, outerRadius * 1.7, 0, Math.PI * 2);
            ctx.fill();

            drawHex(ctx, center.x, center.y, 58, time * 0.00015);
            var fill = ctx.createLinearGradient(center.x - 58, center.y - 58, center.x + 58, center.y + 58);
            fill.addColorStop(0, 'rgba(35, 93, 208, 0.98)');
            fill.addColorStop(1, 'rgba(12, 30, 82, 0.98)');
            ctx.fillStyle = fill;
            ctx.fill();
            ctx.strokeStyle = 'rgba(124, 196, 255, 0.82)';
            ctx.lineWidth = 2;
            ctx.stroke();

            ctx.fillStyle = '#fff';
            ctx.font = '700 17px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.shadowColor = 'rgba(91, 155, 255, 0.65)';
            ctx.shadowBlur = 12;
            ctx.fillText('OBOT', center.x, center.y - 8);
            ctx.shadowBlur = 0;
            ctx.fillStyle = 'rgba(204, 224, 255, 0.9)';
            ctx.font = '500 9px monospace';
            ctx.fillText('GATEWAY', center.x, center.y + 11);

            ctx.save();
            ctx.fillStyle = 'rgba(154, 167, 189, 0.48)';
            ctx.font = '500 10px monospace';
            ctx.textAlign = 'center';
            ctx.translate(30, center.y);
            ctx.rotate(-Math.PI / 2);
            ctx.fillText('AI CLIENTS', 0, 0);
            ctx.restore();

            ctx.save();
            ctx.fillStyle = 'rgba(154, 167, 189, 0.48)';
            ctx.font = '500 10px monospace';
            ctx.textAlign = 'center';
            ctx.translate(LOGICAL_WIDTH - 30, center.y);
            ctx.rotate(Math.PI / 2);
            ctx.fillText('MCP SERVERS', 0, 0);
            ctx.restore();

            ctx.restore();
        }

        function tick(time) {
            draw(time);

            if (!reduceMotionQuery.matches) {
                frameId = window.requestAnimationFrame(tick);
            }
        }

        function start() {
            if (frameId) {
                window.cancelAnimationFrame(frameId);
            }

            if (reduceMotionQuery.matches) {
                draw(performance.now());
                return;
            }

            frameId = window.requestAnimationFrame(tick);
        }

        if (window.ResizeObserver) {
            new ResizeObserver(resize).observe(canvas);
        } else {
            window.addEventListener('resize', resize);
        }

        if (reduceMotionQuery.addEventListener) {
            reduceMotionQuery.addEventListener('change', start);
        } else if (reduceMotionQuery.addListener) {
            reduceMotionQuery.addListener(start);
        }
        resize();
        start();
    }

    function initAll() {
        document.querySelectorAll(SELECTOR).forEach(initDiagram);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
}());
