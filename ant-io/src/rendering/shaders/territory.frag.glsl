#version 300 es
// =============================================================================
// ANCHOR:SHADER_TERRITORY_FRAG
// Description: Renders colony territory ownership as a solid color fill.
//              Reads pheromone texture A channel for ownership encoding:
//                0.0 = unclaimed, 0.25 = colony 0, 0.75 = colony 1
//              Fills owned cells with colony color at low alpha.
// =============================================================================

precision mediump float;

in vec2 v_uv;

uniform sampler2D u_pheromone_tex;
uniform vec3      u_colony0_color;
uniform vec3      u_colony1_color;

out vec4 fragColor;

void main() {
    vec4 signals = texture(u_pheromone_tex, v_uv);
    float owner = signals.a;

    if (owner > 0.5) {
        // Colony 1
        float intensity = max(signals.r, max(signals.g, signals.b));
        float alpha = 0.15 + intensity * 0.2;
        fragColor = vec4(u_colony1_color, alpha);
    } else if (owner > 0.1) {
        // Colony 0
        float intensity = max(signals.r, max(signals.g, signals.b));
        float alpha = 0.15 + intensity * 0.2;
        fragColor = vec4(u_colony0_color, alpha);
    } else {
        discard;
    }
}
