#version 300 es
// =============================================================================
// ANCHOR:SHADER_PHEROMONE_FRAG
// Description: Renders colony-owned pheromone markers with competitive coloring.
//              RGBA texture channels:
//                R = HOME_PATH intensity
//                G = FOOD_FOUND intensity
//                B = DANGER intensity
//                A = colony ownership (0.0=unclaimed, 0.25=colony0, 0.75=colony1)
//
//              Each colony's markers are tinted with its team color so players
//              can visually distinguish trail ownership and see competition.
// =============================================================================

precision mediump float;

in vec2 v_uv;

uniform sampler2D u_pheromone_tex;
uniform float     u_alpha_scale;
uniform vec3      u_color_home;     // HOME_PATH  base color (green)
uniform vec3      u_color_food;     // FOOD_FOUND base color (yellow)
uniform vec3      u_color_danger;   // DANGER     base color (red)
uniform vec3      u_colony0_color;  // Colony 0 tint (red/orange)
uniform vec3      u_colony1_color;  // Colony 1 tint (blue)

out vec4 fragColor;

void main() {
    vec4 signals = texture(u_pheromone_tex, v_uv);
    float ownerA = signals.a;

    // Determine colony tint from A channel ownership encoding
    vec3 colonyTint;
    if (ownerA > 0.5) {
        colonyTint = u_colony1_color;  // Colony 1 (blue team)
    } else if (ownerA > 0.1) {
        colonyTint = u_colony0_color;  // Colony 0 (red team)
    } else {
        discard; // unclaimed cell with no signal
    }

    // Maximum signal for discard threshold
    float maxSignal = max(max(signals.r, signals.g), signals.b);
    if (maxSignal < 0.005) discard;

    // Additive color: blend signal base colors with colony tint (70% signal, 30% colony)
    vec3 col = signals.r * mix(u_color_home,   colonyTint, 0.35)
             + signals.g * mix(u_color_food,   colonyTint, 0.35)
             + signals.b * mix(u_color_danger, colonyTint, 0.35);

    float alpha = maxSignal * u_alpha_scale;

    fragColor = vec4(col, clamp(alpha, 0.0, 1.0));
}
