#version 300 es
// =============================================================================
// ANCHOR:SHADER_OVERLAY_FRAG
// Description: Corner panel data overlay rendering fitness history curves.
//              Renders a dark semi-transparent panel in the bottom-left,
//              with two line graphs of best fitness per generation.
//              All rendering is done procedurally in the fragment shader.
// Uniforms:
//   u_gen_fitness_c0[64] - best fitness per generation for colony 0 (last 64 gens)
//   u_gen_fitness_c1[64] - best fitness per generation for colony 1
//   u_history_count      - number of valid entries in the above arrays
//   u_fitness_max        - max fitness value for normalization
//   u_colony0_color      - colony 0 line color
//   u_colony1_color      - colony 1 line color
// =============================================================================

precision mediump float;

in vec2 v_uv;   // [0,1] over full screen

uniform float u_gen_fitness_c0[64];
uniform float u_gen_fitness_c1[64];
uniform int   u_history_count;
uniform float u_fitness_max;
uniform vec3  u_colony0_color;
uniform vec3  u_colony1_color;

out vec4 fragColor;

// Panel occupies bottom-left [0,0.22] × [0,0.18] of screen UV
const vec2 PANEL_MIN = vec2(0.01, 0.01);
const vec2 PANEL_MAX = vec2(0.25, 0.20);
const float LINE_W = 0.004;

float sdfLine(vec2 uv, vec2 a, vec2 b) {
    vec2 pa = uv - a, ba = b - a;
    float h = clamp(dot(pa, ba) / dot(ba, ba), 0.0, 1.0);
    return length(pa - ba * h);
}

void main() {
    // Outside panel: discard
    if (v_uv.x < PANEL_MIN.x || v_uv.x > PANEL_MAX.x ||
        v_uv.y < PANEL_MIN.y || v_uv.y > PANEL_MAX.y) {
        discard;
    }

    // Panel-local UV [0,1]
    vec2 puv = (v_uv - PANEL_MIN) / (PANEL_MAX - PANEL_MIN);

    // Base panel: dark semi-transparent
    vec3  col   = vec3(0.04, 0.05, 0.08);
    float alpha = 0.78;

    int count = min(u_history_count, 64);
    if (count > 1) {
        float norm = max(u_fitness_max, 1.0);

        for (int i = 1; i < 64; i++) {
            if (i >= count) break;
            float x0 = float(i - 1) / float(count - 1);
            float x1 = float(i)     / float(count - 1);
            float y0c0 = u_gen_fitness_c0[i - 1] / norm;
            float y1c0 = u_gen_fitness_c0[i]     / norm;
            float y0c1 = u_gen_fitness_c1[i - 1] / norm;
            float y1c1 = u_gen_fitness_c1[i]     / norm;

            // Clamp to panel interior [0.05,0.95]
            y0c0 = clamp(y0c0, 0.05, 0.95);
            y1c0 = clamp(y1c0, 0.05, 0.95);
            y0c1 = clamp(y0c1, 0.05, 0.95);
            y1c1 = clamp(y1c1, 0.05, 0.95);

            float d0 = sdfLine(puv, vec2(x0, y0c0), vec2(x1, y1c0));
            float d1 = sdfLine(puv, vec2(x0, y0c1), vec2(x1, y1c1));

            if (d0 < LINE_W) col = mix(col, u_colony0_color, smoothstep(LINE_W, 0.0, d0));
            if (d1 < LINE_W) col = mix(col, u_colony1_color, smoothstep(LINE_W, 0.0, d1));
        }
    }

    // Grid lines (faint)
    float gx = mod(puv.x, 0.25);
    float gy = mod(puv.y, 0.25);
    if (gx < 0.005 || gy < 0.005) col += vec3(0.04);

    fragColor = vec4(col, alpha);
}
